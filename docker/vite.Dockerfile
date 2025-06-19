# Dockerfile for Vite (Node.js)
FROM node:latest as node

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . .

EXPOSE 5173
CMD ["npm", "run", "dev"]
