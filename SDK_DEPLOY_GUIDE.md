Dưới đây là **tập lệnh tài liệu định dạng Markdown (.md)** đã được chuẩn hóa, loại bỏ phần lặp, sắp xếp lại mục lục và phù hợp để lưu trực tiếp thành file ví dụ:
`SDK_DEPLOY_GUIDE.md`

Bạn có thể copy **nguyên khối** nội dung này để sử dụng.

---

````md
# Hướng dẫn SDK & Triển khai Dự án

Tài liệu này mô tả cách **kết thúc phiên làm việc hiện tại** và cung cấp **ghi chú SDK**, **thiết lập môi trường**, **cấu hình**, **build** và **triển khai** cho toàn bộ hệ thống dự án Node.js / Next.js.

---

## 0. Kết thúc phiên / quá trình hiện tại

Trước khi triển khai hoặc cấu hình lại hệ thống, hãy đảm bảo các tiến trình đang chạy được dừng đúng cách.

### Dừng Node.js / PM2
```bash
pm2 stop all
pm2 delete all
````

### Dừng tiến trình chạy thủ công

```bash
Ctrl + C
```

---

## 1. Tổng quan cấu trúc dự án

Dự án bao gồm các module chính:

* **Server/**
  Backend API trung tâm

  * Node.js, Express
  * Sequelize ORM (MySQL)
  * Xác thực, nghiệp vụ, API cho frontend & agent

* **cms-agent/**
  Agent/CMS client

  * Node.js, Express
  * Kết nối Server backend

* **cms-master-develop/**
  Phiên bản CMS/Agent mở rộng hoặc module quản trị

* **frontend/**
  Giao diện người dùng công khai

  * Next.js / React

* **phpmyadmin/**
  Công cụ quản lý MySQL

* **fromdangki/**
  Frontend tĩnh / trang đăng ký độc lập

---

## 2. Thiết lập môi trường phát triển

### 2.1 Yêu cầu hệ thống

* **Node.js**: >= 16
* **npm** hoặc **yarn**
* **MySQL Server**
* **Redis Server**

Kiểm tra:

```bash
node -v
npm -v
```

---

### 2.2 Cài đặt dependency

Thực hiện cho **mỗi module Node.js**:

```bash
cd /var/app/Server
npm install
# hoặc
yarn install
```

Áp dụng tương tự cho:

* `cms-agent/`
* `cms-master-develop/`
* `frontend/`

---

## 3. Cấu hình môi trường (.env)

### 3.1 Backend Server

Tạo file `Server/.env` từ `.env.example`

```env
ENV_ENVIROMENT=develop
APPLICATION_NAME=CSN365_SYSTEM_CORE
APPLICATION_SCRET_KEY=your_application_secret_key

JWT_SCRET_KEY=your_jwt_secret_key
JWT_EXPIRES_IN=1d
SESSION_SECRET=your_session_secret

PORT=8009
PORT_SOCKET_IO=8008

MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_USERNAME=root
MYSQL_PASSWORD=your_mysql_password
MYSQL_DATABASE=your_database_name

REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=your_redis_password

SITE_DOMAIN=your_site_domain.com

TELEGRAM_TOKEN_DEPLOY=
TELEGRAM_GROUP_DEPLOY=
```

---

### 3.2 Frontend (Next.js)

#### Development

`frontend/.env.development`

```env
NEXT_PUBLIC_AUTH_API_URL=http://api1.6688b.online/
NEXT_PUBLIC_API_URL=http://api.6688b.online/
```

#### Production

`frontend/.env.production`

```env
NEXT_PUBLIC_AUTH_API_URL=https://api1.6688b.online/
NEXT_PUBLIC_API_URL=https://api.6688b.online/
```

---

### 3.3 CMS Agent

`cms-agent/.env`

```env
ENV_ENVIROMENT=develop

JWT_SCRET_KEY=your_jwt_secret_key
JWT_EXPIRES_IN=1d
SESSION_SECRET=your_session_secret

PORT=3456

MONGO_HOST=localhost
MONGO_PORT=26922
MONGO_USERNAME=your_mongo_username
MONGO_PASSWORD=your_mongo_password
MONGO_DATABASE=your_mongo_database

REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=your_redis_password
REDIS_PREFIX=master-agent

API_SERVER=http://localhost:8009/api/agent
```

---

## 4. Database Migration & Seeder

### 4.1 Sequelize CLI config

`Server/.sequelizerc`

```js
const path = require('path');

module.exports = {
  config: path.resolve(__dirname, 'src/sequelize/config/config.json'),
  'models-path': path.resolve(__dirname, 'src/models'),
  'seeders-path': path.resolve(__dirname, 'src/sequelize/seeders'),
  'migrations-path': path.resolve(__dirname, 'src/sequelize/migrations')
};
```

---

### 4.2 Chạy migration

```bash
cd /var/app/Server
npx sequelize-cli db:migrate
```

### 4.3 Seeder (tuỳ chọn)

```bash
npx sequelize-cli db:seed:all
# hoặc
npx sequelize-cli db:seed --seed 20251223-add-test-users.js
```

---

## 5. Khởi động hệ thống

### 5.1 Backend Server

```bash
cd /var/app/Server
npm start
# hoặc
npm run dev
```

### 5.2 CMS Agent

```bash
cd /var/app/cms-agent
npm start
# hoặc
npm run dev
```

### 5.3 Frontend

```bash
cd /var/app/frontend
npm run build
npm start
```

---

## 6. Build & Deploy

### 6.1 Backend (Server / Agent)

```bash
npm install --production
pm2 start ecosystem.config.js
```

Không có bước build asset phức tạp.

---

### 6.2 Frontend (Next.js)

```bash
npm run build
```

Deploy options:

* **Next.js server** (`npm start`)
* **Static export** (`next export`)

---

## 7. Lưu ý triển khai Production

* Reverse Proxy: **Nginx / Apache**
* Process manager: **PM2**
* HTTPS: SSL Certificate
* Environment variables phải đầy đủ
* Tránh trùng port (3000 / 8009 / 3456)

---

## 8. Ghi chú SDK & Phát triển

* **Sequelize ORM**

  * Model
  * Migration
  * Association

* **Middleware**

  * CmsAuthenticate
  * CmsPermission

* **Dynamic UI**

  * UIPageModel
  * UIBlockModel
  * UIConfigAuditLogModel
  * MediaAssetModel

* **CMS APIs**

  * `/cms/ui/*`
  * `/cms/media/*`

* **Public APIs**

  * `/api/ui/config/:slug`

* **Upload**

  * Multer (MediaManagementController)

---

## Kết luận

Tài liệu này hoàn tất ghi chú SDK, cấu hình, build và deploy cho toàn bộ hệ thống.
Phiên làm việc hiện tại được xem là **đã kết thúc**.

---

```

Nếu bạn muốn:
- tách tài liệu thành **README.md + DEPLOY.md**
- hoặc tạo **Docker / docker-compose**
- hoặc chuẩn hóa thành **internal SDK doc cho team**

hãy nói rõ, tôi sẽ chuẩn hóa tiếp.
```
