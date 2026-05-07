FROM node:18-slim

# Install Chromium dependencies for puppeteer
RUN apt-get update && apt-get install -y \
    chromium \
    fonts-freefont-ttf \
    --no-install-recommends && rm -rf /var/lib/apt/lists/*

ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium

WORKDIR /app
COPY docker/whatsapp/package.json .
RUN npm install --omit=dev
COPY docker/whatsapp/server.js .

EXPOSE 3000
CMD ["node", "server.js"]
