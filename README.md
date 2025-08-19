# 🛒 Batch Data Pipeline for Retail Analytics

A Dockerized **batch-processing** pipeline that ingests, cleans, and aggregates retail transactions for **quarterly analytics**.  
Built with **Laravel**, **MySQL**, and **Docker** using a microservices approach.

> **Course:** Data Engineering (DLMDSEDE02) — Portfolio Project  
> **Phase:** Development/Reflection (Phase 2) ✔️

---

## 📂 Repository
**GitHub:** [Batch-Processing-Data-Pipeline](https://github.com/zahoormsc24/Batch-Processing-Data-Pipeline)

---

## 📚 Table of Contents
1. [Overview](#-overview)  
2. [Architecture](#-architecture)  
3. [Repository Structure](#-repository-structure)  
4. [Prerequisites](#-prerequisites)  
5. [Quickstart](#-quickstart)  
   - [A. Using This Repository](#a-using-this-repository)  
   - [B. Starting From Scratch](#b-starting-from-scratch)  
6. [Configuration](#-configuration)  
7. [Database Schema](#-database-schema)  
8. [Running the Pipeline](#-running-the-pipeline)  
9. [Troubleshooting](#-troubleshooting)  
10. [Roadmap](#-roadmap)  
11. [License & Acknowledgements](#-license--acknowledgements)  
12. [Maintainer](#-maintainer)

---

## 🔎 Overview
This repository implements a **batch data pipeline** for the UCI **Online Retail II** dataset (1M+ time-referenced transactions).  

The pipeline:
- **Ingests** CSV data into MySQL (`transactions` table)  
- **Cleans** invalid rows (`clean_transactions` table)  
- **Aggregates** quarterly sales per product & customer (`quarterly_sales` table)  

It is designed for **reliability**, **reproducibility**, and **maintainability**, using **Docker Compose**, **Laravel Artisan commands**, and **database migrations**.

---

## 🏗 Architecture
*(Insert your architecture diagram in `docs/architecture.png` once ready)*

**Pipeline Flow:**
Excel (.xlsx) → CSV → ingestion → transactions
↓
preprocessing → clean_transactions
↓
aggregation → quarterly_sales


**Services**
- **MySQL** – central database  
- **Ingestion** – loads CSV → transactions  
- **Preprocessing** – filters invalid rows → clean_transactions  
- **Aggregation** – computes quarterly sales → quarterly_sales  

---

## 📁 Repository Structure
Batch-Processing-Data-Pipeline/
├── docker-compose.yml
├── docs/
│ └── architecture.png
├── ingestion/ # Laravel app (commands + migrations)
│ ├── app/Console/Commands/
│ │ ├── IngestCsv.php
│ │ ├── PreprocessData.php
│ │ └── AggregateQuarterly.php
│ ├── database/migrations/
│ │ ├── create_transactions_table.php
│ │ ├── create_clean_transactions_table.php
│ │ └── create_quarterly_sales_table.php
│ └── storage/app/online_retail_II.csv


---

## ✅ Prerequisites
- Docker & Docker Compose  
- PHP 8.x & Composer (if running Laravel locally)  
- LibreOffice (for `.xlsx → .csv`) or manual CSV export  

---

## ⚡ Quickstart

### A) Using This Repository
```bash
# Clone repo
git clone https://github.com/zahoormsc24/Batch-Processing-Data-Pipeline.git
cd Batch-Processing-Data-Pipeline

# Start containers
docker compose up -d --build

# Enter Laravel app container
docker compose exec ingestion bash

# Run migrations
php artisan migrate

# Place CSV file into:
#   host:   ingestion/storage/app/online_retail_II.csv
#   inside: storage/app/online_retail_II.csv

# Run pipeline
php artisan ingest:csv
php artisan preprocess:data
php artisan aggregate:quarterly
```
B) Starting From Scratch
mkdir retail-pipeline && cd retail-pipeline
composer create-project laravel/laravel ingestion

# Convert dataset to CSV
libreoffice --headless --convert-to csv online_retail_II.xlsx

# Write docker-compose.yml and repeat steps above

⚙️ Configuration
.env (inside ingestion container):
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306       # inside containers
DB_DATABASE=retail_db
DB_USERNAME=retail_user
DB_PASSWORD=retail_pass

Host MySQL access:
Host: 127.0.0.1

Port: 3307 (mapped in docker-compose.yml)

User: retail_user

Pass: retail_pass

🗄 Database Schema
transactions (raw data)
| Column      | Type    | Notes                  |
| ----------- | ------- | ---------------------- |
| Invoice     | string  | Invoice number         |
| StockCode   | string  | Product code           |
| Description | string  | Product description    |
| Quantity    | int     | Quantity purchased     |
| InvoiceDate | string  | Transaction date/time  |
| Price       | decimal | Price per unit         |
| CustomerID  | string  | Customer ID (nullable) |
| Country     | string  | Customer country       |

clean_transactions (validated data)
Same schema as transactions, excluding invalid rows.
quarterly_sales (aggregated data)
| Column          | Type    | Notes                     |
| --------------- | ------- | ------------------------- |
| year\_quarter   | string  | e.g., `2011-Q1`           |
| StockCode       | string  | Product code              |
| CustomerID      | string  | Customer ID               |
| total\_quantity | int     | Sum of quantities         |
| total\_sales    | decimal | Sum of `Quantity × Price` |

▶️ Running the Pipeline
php artisan migrate
php artisan ingest:csv
php artisan preprocess:data
php artisan aggregate:quarterly

Notes:
ingest:csv → expects file at storage/app/online_retail_II.csv

preprocess:data → cleans invalid data in batches of 1000 rows

aggregate:quarterly → uses MySQL YEAR() + QUARTER() for grouping
🧰 Troubleshooting
Database connection error? Use DB_HOST=mysql and DB_PORT=3306 inside containers.

App can’t connect from host? Use 127.0.0.1:3307.

Placeholder errors / slow inserts? Insert rows in chunks (1000 rows per batch).

Date parsing issues? Adjust STR_TO_DATE format string to match your CSV (dd/mm/yyyy HH:MM).

CSV not found? Ensure file is in ingestion/storage/app/online_retail_II.csv.

🗺 Roadmap

REST API for aggregated data

Add CI/CD & container healthchecks

Deploy pipeline to cloud (AWS, GCP, Azure)

Add real-time streaming pipeline (Kafka, Spark)

© License & Acknowledgements

Dataset: Online Retail II — UCI Machine Learning Repository

Project developed for IU DLMDSEDE02 coursework
👤 Maintainer
zahoormsc24
