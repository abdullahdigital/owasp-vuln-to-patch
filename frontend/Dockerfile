FROM node:16-alpine

# Set working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy application code
COPY . .

# Build the Svelte app for production
RUN npm run build

# Expose port 5173
EXPOSE 5173

# Command to run the Svelte app
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]