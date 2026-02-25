# Dockerfile para Frontend Vue 3
FROM node:20-alpine

# Diretório de trabalho
WORKDIR /app

# Copiar package.json e package-lock.json
COPY package*.json ./

# Instalar dependências
RUN npm install

# Copiar código fonte
COPY . .

# Expor porta do Vite
EXPOSE 5173

# Comando padrão (dev server)
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
