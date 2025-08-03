const express = require("express");
const cors = require("cors");
const { Kafka } = require("kafkajs");
require("dotenv").config();

const app = express();
const PORT = process.env.PORT || 3000;

const kafka = new Kafka({
	clientId: "email-service",
	brokers: (process.env.KAFKA_BROKERS || "kafka:29092").split(","),
	retry: {
		initialRetryTime: 100,
		retries: 8,
	},
});

const consumer = kafka.consumer({ groupId: "email-service-group" });
const producer = kafka.producer();

let kafkaConnected = false;

class MockEmailService {
	constructor() {
		this.sentEmails = [];
		this.userEmails = new Set();
		this.processedUsers = new Set();
	}

	async sendWelcomeEmail(userData) {
		if (this.processedUsers.has(userData.id)) {
			return null;
		}

		const emailData = {
			to: userData.email,
			subject: "Welcome to Our Platform!",
			body: `Hello ${userData.name}. Welcome to our platform!`,
			timestamp: new Date().toISOString(),
			user_id: userData.id,
		};

		await new Promise((resolve) => setTimeout(resolve, 1000));

		this.sentEmails.push(emailData);
		this.userEmails.add(userData.email);
		this.processedUsers.add(userData.id);

		console.log(
			`Welcome email sent to ${userData.email} for user ID: ${userData.id}`,
		);

		return emailData;
	}

	getSentEmails() {
		return this.sentEmails;
	}

	getEmailCount() {
		return this.sentEmails.length;
	}

	getUserEmails() {
		return Array.from(this.userEmails);
	}

	getUserEmailsCount() {
		return this.userEmails.size;
	}

	getProcessedUsersCount() {
		return this.processedUsers.size;
	}
}

const emailService = new MockEmailService();

async function setupKafkaConsumer() {
	try {
		await consumer.connect();
		await consumer.subscribe({ topic: "user-events", fromBeginning: true });

		await consumer.run({
			eachMessage: async ({ topic, message }) => {
				try {
					const eventData = JSON.parse(message.value.toString());
					console.log(`Received message from topic ${topic}:`, eventData);

					if (eventData.event === "user.created") {
						console.log(
							`Processing user created event for: ${eventData.data.email}`,
						);

						const emailResult = await emailService.sendWelcomeEmail(
							eventData.data,
						);

						if (emailResult) {
							await producer.connect();
							await producer.send({
								topic: "email-notifications",
								messages: [
									{
										key: eventData.data.id.toString(),
										value: JSON.stringify({
											event: "email.sent",
											timestamp: new Date().toISOString(),
											data: {
												user_id: eventData.data.id,
												email: eventData.data.email,
												email_type: "welcome",
												status: "sent",
											},
											metadata: {
												source: "email-service",
												version: "1.0.0",
											},
										}),
									},
								],
							});
							await producer.disconnect();

							console.log(
								`Welcome email processed successfully for user: ${eventData.data.email}`,
							);
						}
					}
				} catch (error) {
					console.error("Error processing Kafka message:", error);
				}
			},
		});

		kafkaConnected = true;
	} catch (error) {
		console.error("Failed to setup Kafka consumer:", error);
		kafkaConnected = false;
	}
}

async function applicationShutdown() {
	await consumer.disconnect();
	process.exit(0);
}

process.on("SIGTERM", applicationShutdown);
process.on("SIGINT", applicationShutdown);

app.use(cors());
app.use(express.json());

app.get("/external", (_req, res) => {
	const externalData = {
		message: "External microservice health check",
		timestamp: new Date().toISOString(),
		status: "healthy",
		service: {
			name: "node-microservice",
			version: "1.0.0",
			uptime: process.uptime(),
			pid: process.pid,
			platform: process.platform,
			node_version: process.version,
		},
		kafka: {
			connected: kafkaConnected,
			group_id: "email-service-group",
			topics: ["user-events"],
		},
		email_service: {
			emails_sent: emailService.getEmailCount(),
			unique_users: emailService.getUserEmailsCount(),
			processed_users: emailService.getProcessedUsersCount(),
			user_emails: emailService.getUserEmails(),
			status: "operational",
		},
	};

	res.json(externalData);
});

app.use("*", (_req, res) => {
	res.status(404).json({
		error: "Endpoint not found",
		availableEndpoints: ["GET /external"],
	});
});

async function startServer() {
	try {
		await setupKafkaConsumer();

		app.listen(PORT, () => {
			console.log(`Node.js microservice running on port ${PORT}`);
			console.log(`Kafka consumer listening on topic: user-events`);
		});
	} catch (error) {
		console.error("Failed to start server:", error);
		process.exit(1);
	}
}

startServer();
