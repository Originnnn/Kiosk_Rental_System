# Kiosk Rental System (Hệ thống Quản lý và Cho thuê Kiosk)

Kiosk Rental System là một ứng dụng nền web toàn diện được xây dựng bằng **Laravel**, chuyên dùng để quản lý hệ thống kiosk tại các bến xe, khu thương mại hoặc khu vực công cộng. Hệ thống cung cấp cổng thông tin tương tác cho khách hàng và bảng điều khiển quản trị mạnh mẽ cho ban quản lý.

## 🌟 Các tính năng nổi bật

### 1. Dành cho Khách hàng (Public Portal)
- **Sơ đồ Kiosk tương tác (Interactive Sitemap):** Bản đồ trực quan hỗ trợ Pan/Zoom, giúp khách hàng dễ dàng định vị các kiosk trong các khu vực (Zone A, Zone B, v.v.).
- **Danh mục & Tìm kiếm:** Hiển thị danh sách kiosk theo trạng thái (Trống, Đang thuê, Bảo trì), tích hợp tính năng lọc thông minh theo khu vực và thanh tìm kiếm.
- **Giao diện Chi tiết Kiosk hiện đại:** Hỗ trợ xem hình ảnh 360/nhiều góc độ bằng Slider vuốt/kéo (Drag-to-Scroll), xem tiện ích đi kèm và thông tin diện tích.
- **Đăng ký thuê trực tuyến:** Khách hàng có thể dễ dàng điền form đăng ký thuê kiosk ngay trên hệ thống.

### 2. Dành cho Quản trị viên (Admin Dashboard)
- **Quản lý Kiosk:** Thêm, sửa, xóa, và cập nhật trạng thái kiosk. Hỗ trợ thao tác nhanh (Quick Action) như "Bảo trì" hoặc "Mở hoạt động lại". Quản lý kho hình ảnh vật lý của từng kiosk.
- **Quản lý Hợp đồng (Contracts) & Khách hàng:** Quản lý vòng đời hợp đồng từ khi tiếp nhận yêu cầu (`RentalRequest`) đến khi ký kết và theo dõi.
- **Quản lý Tài chính (Payments):** Quản lý tiến độ thanh toán (`ContractPaymentSchedule`), lịch sử giao dịch và hóa đơn.
- **Hệ thống Cảnh báo tự động (Alerts & Notifications):** Cron job chạy ngầm tự động quét và gửi thông báo khi có khoản thanh toán quá hạn, giúp Admin phản ứng kịp thời. Khi click vào thông báo, hệ thống điều hướng trực tiếp đến trang chi tiết hợp đồng.
- **Phân quyền (Roles):** Hỗ trợ đa dạng các nhóm người dùng: Admin, Manager, và Employee với các mức độ truy cập khác nhau.

## 🛠 Công nghệ sử dụng

- **Backend:** PHP >= 8.3, Laravel 13.x
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js, Vanilla JS (Panzoom.js)
- **Database:** MySQL
- **Khác:** Laravel Commands (Background Jobs, ETL), Eloquent ORM.

## 📂 Cấu trúc dự án chính

- `app/Models/`: Chứa các models như `Kiosk`, `Contract`, `Customer`, `ContractPaymentSchedule`, `RentalRequest`,...
- `app/Http/Controllers/`: Xử lý logic chia thành 2 nhóm chính: `PortalController` (dành cho người dùng ngoài) và nhóm Admin Controller (quản lý).
- `app/Console/Commands/`: Chứa các lệnh Artisan tự định nghĩa như `SyncKioskImages` (chuẩn hóa ảnh) và `CheckOverduePayments` (quét nợ).
- `resources/views/`: 
  - `/public`: Giao diện dành cho khách hàng.
  - `/admin`: Bảng điều khiển quản trị.

## 🚀 Hướng dẫn Cài đặt & Chạy dự án

1. **Clone repository và cài đặt thư viện PHP:**
   ```bash
   git clone <repo_url>
   cd kiosk_rental
   composer install
   ```

2. **Cài đặt thư viện Frontend:**
   ```bash
   npm install
   npm run build # hoặc npm run dev khi phát triển
   ```

3. **Cấu hình môi trường (.env):**
   - Copy file `.env.example` thành `.env`.
   - Cập nhật thông tin kết nối Cơ sở dữ liệu (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
   - Đảm bảo thiết lập đúng `APP_URL` và `APP_URL_BASE` để hệ thống định tuyến chính xác các Subdomain (VD: `admin.kiosk.localhost`).

4. **Khởi tạo Database & Dữ liệu mẫu:**
   ```bash
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```
   *(Lưu ý: Lệnh migrate:fresh sẽ xóa toàn bộ dữ liệu cũ, chỉ dùng trong môi trường phát triển).*

5. **Chạy ứng dụng:**
   ```bash
   php artisan serve
   ```
   - Trang Khách hàng: `http://localhost:8000` (hoặc domain ảo bạn cấu hình)
   - Trang Admin: `http://admin.localhost:8000`

## ⚙️ Các lệnh Cron/Commands thường dùng

- **Đồng bộ và chuẩn hóa hình ảnh Kiosk:**
  ```bash
  php artisan app:sync-kiosk-images
  ```
- **Quét và thông báo nợ quá hạn:**
  ```bash
  php artisan app:check-overdue-payments
  ```

---
*Dự án Kiosk Rental System được thiết kế với mục tiêu tự động hóa, minh bạch hóa quy trình cho thuê mặt bằng và nâng cao trải nghiệm của cả người dùng cuối lẫn đội ngũ vận hành.*
