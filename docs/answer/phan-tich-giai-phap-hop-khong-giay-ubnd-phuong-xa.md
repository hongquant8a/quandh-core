# Phân tích giải pháp phần mềm quản lý kỳ họp không giấy tại UBND phường, xã

**Ngày tạo:** 2026-04-28  
**Mục đích:** Phân tích giải pháp quản lý các cuộc họp/kỳ họp không giấy phục vụ UBND phường, xã, bao gồm họp thường xuyên, họp định kỳ, họp chuyên đề và kỳ họp Hội đồng nhân dân.

---

## 1. Bối cảnh và mục tiêu

### 1.1 Bối cảnh nghiệp vụ

UBND phường, xã thường tổ chức nhiều loại cuộc họp với tần suất cao:

- Họp giao ban lãnh đạo, giao ban chuyên môn.
- Họp định kỳ UBND, họp triển khai nhiệm vụ.
- Họp chuyên đề theo lĩnh vực.
- Kỳ họp Hội đồng nhân dân.
- Phiên họp lấy ý kiến, biểu quyết, chất vấn.
- Họp liên thông với tổ dân phố, khu dân cư hoặc đơn vị trực thuộc.

Trong mô hình truyền thống, tài liệu họp thường được in ấn, phát giấy, điều chỉnh thủ công và khó theo dõi lượt xem, điểm danh, biểu quyết, ý kiến chất vấn. Giải pháp họp không giấy cần số hóa toàn bộ vòng đời cuộc họp từ khâu chuẩn bị, thông báo, phát hành tài liệu, điểm danh, điều hành chương trình, biểu quyết, chất vấn đến lưu trữ sau họp.

### 1.2 Mục tiêu giải pháp

- Quản lý tập trung danh sách các cuộc họp theo từng tổ chức/phường/xã.
- Số hóa tài liệu họp, hạn chế in ấn và kiểm soát quyền truy cập tài liệu.
- Hỗ trợ điểm danh người tham dự dựa trên danh sách mời.
- Hỗ trợ biểu quyết theo chương trình họp, có cập nhật kết quả thời gian thực.
- Hỗ trợ đăng ký thảo luận, chất vấn, điều phối phát biểu.
- Cho phép từng người dùng ghi chú cá nhân theo cuộc họp/tài liệu.
- Thông báo lịch họp, nhắc lịch họp và cảnh báo thay đổi thông tin.
- Cung cấp thống kê, lịch sử xử lý và dữ liệu phục vụ báo cáo.

---

## 2. Phạm vi chức năng

### 2.1 Nhóm chức năng chính

1. **Quản lý loại cuộc họp**
   - Tạo danh mục loại cuộc họp.
   - Phân loại theo họp thường xuyên, định kỳ, chuyên đề, HĐND, khẩn cấp.
   - Cấu hình trạng thái, thứ tự hiển thị, mô tả.

2. **Quản lý nhóm người dự họp**
   - Tạo danh mục nhóm người dự họp riêng của module họp không giấy.
   - Phân nhóm theo nghiệp vụ: thường trực UBND, đại biểu HĐND, lãnh đạo phòng ban, thư ký, khách mời thường xuyên.
   - Hỗ trợ chọn nhanh nhóm khi lập danh sách mời họp.
   - Cho phép cấu hình trạng thái, mô tả, thứ tự hiển thị.

3. **Quản lý người dự họp**
   - Quản lý danh bạ người dự họp riêng cho module.
   - Liên kết với tài khoản hệ thống lõi qua `user_id` khi là người dùng nội bộ.
   - Lưu thông tin nghiệp vụ tại thời điểm sử dụng: chức vụ, đơn vị, nhóm, loại người dự họp.
   - Hỗ trợ người dự họp ngoài hệ thống nếu cần mời khách nhưng chưa cấp tài khoản.

4. **Công khai cuộc họp và tài liệu**
   - Có trang chủ công khai để hiển thị các cuộc họp được phép công khai.
   - Mỗi cuộc họp có chế độ công khai riêng.
   - Mỗi tài liệu thuộc cuộc họp có chế độ công khai riêng.
   - Chỉ các tài liệu vừa thuộc cuộc họp công khai vừa được bật công khai mới hiển thị cho người ngoài.
   - Cho phép công khai thông tin cần thiết nhưng vẫn giữ riêng tư các nội dung nội bộ, tài liệu mật, điểm danh, biểu quyết và ghi chú.

5. **Quản lý danh sách cuộc họp**
   - Tạo, cập nhật, hủy, kết thúc, lưu trữ cuộc họp.
   - Tìm kiếm theo tiêu đề, loại cuộc họp, trạng thái, thời gian, người chủ trì.
   - Xem lịch họp theo ngày/tuần/tháng.
   - Theo dõi lượt xem và mức độ tiếp cận tài liệu.

6. **Quản lý thông tin chi tiết cuộc họp**
   - Tiêu đề cuộc họp.
   - Mô tả cuộc họp.
   - Thời gian bắt đầu.
   - Thời gian kết thúc.
   - Địa chỉ họp/phòng họp.
   - Người chủ trì.
   - Người điều hành.
   - Danh sách người tham dự.
   - Chương trình cuộc họp.
   - Lượt xem cuộc họp.
   - Loại cuộc họp.

7. **Quản lý tài liệu cuộc họp**
   - Tài liệu theo cuộc họp hoặc theo từng nội dung chương trình.
   - Tải lên nhiều tệp, phân loại tài liệu, sắp xếp thứ tự.
   - Phát hành/thu hồi tài liệu.
   - Theo dõi lượt xem/tải tài liệu.
   - Kiểm soát tài liệu nội bộ, mật, chỉ dành cho nhóm người tham dự cụ thể.

8. **Quản lý điểm danh**
   - Tạo danh sách điểm danh từ danh sách người tham dự.
   - Điểm danh thủ công bởi thư ký.
   - Điểm danh qua QR code hoặc xác nhận trên thiết bị cá nhân.
   - Theo dõi trạng thái: chưa điểm danh, có mặt, vắng mặt, đến muộn, xin vắng.
   - Xuất danh sách điểm danh.

9. **Quản lý biểu quyết**
   - Tạo danh sách chương trình/nội dung biểu quyết.
   - Cấu hình hình thức biểu quyết: tán thành, không tán thành, không ý kiến; hoặc đồng ý/không đồng ý.
   - Mở/đóng biểu quyết theo thời gian thực.
   - Theo dõi số người đã biểu quyết, chưa biểu quyết, kết quả tổng hợp.
   - Khóa kết quả sau khi kết thúc.

10. **Quản lý chất vấn và thảo luận**
   - Người tham dự đăng ký thảo luận/chất vấn.
   - Thư ký/người điều hành duyệt, sắp xếp thứ tự phát biểu.
   - Ghi nhận nội dung trả lời, người trả lời, trạng thái xử lý.
   - Phân loại câu hỏi theo chương trình họp hoặc lĩnh vực.

11. **Quản lý ghi chú cá nhân**
   - Người dùng tạo ghi chú riêng theo cuộc họp.
   - Ghi chú theo tài liệu hoặc theo nội dung chương trình.
   - Ghi chú mặc định chỉ người tạo nhìn thấy.
   - Có thể phát triển thêm chức năng chia sẻ ghi chú cho nhóm.

12. **Thông báo và nhắc lịch**
   - Thông báo tạo lịch họp mới.
   - Thông báo thay đổi thời gian, địa điểm, tài liệu, chương trình.
   - Nhắc lịch trước cuộc họp theo mốc cấu hình.
   - Nhắc người chưa xem tài liệu hoặc chưa xác nhận tham dự.

---

## 3. Đối tượng sử dụng

### 3.1 Vai trò nghiệp vụ

1. **Quản trị hệ thống**
   - Cấu hình danh mục, quyền, tổ chức, người dùng.
   - Theo dõi toàn bộ dữ liệu theo phạm vi được phân quyền.

2. **Lãnh đạo/người chủ trì**
   - Xem lịch họp.
   - Theo dõi tài liệu, điểm danh, biểu quyết, chất vấn.
   - Điều hành hoặc kết luận nội dung cuộc họp.

3. **Người điều hành/thư ký cuộc họp**
   - Chuẩn bị thông tin cuộc họp.
   - Quản lý người tham dự, chương trình, tài liệu.
   - Điểm danh, mở biểu quyết, ghi nhận chất vấn.
   - Tổng hợp báo cáo sau họp.

4. **Người tham dự**
   - Nhận thông báo lịch họp.
   - Xem tài liệu họp.
   - Điểm danh/xác nhận tham dự.
   - Biểu quyết, đăng ký thảo luận, gửi chất vấn.
   - Ghi chú cá nhân.

5. **Khách mời**
   - Chỉ xem thông tin/tài liệu được cấp quyền.
   - Có thể điểm danh hoặc tham gia biểu quyết nếu được cấu hình.

### 3.2 Phạm vi dữ liệu theo tổ chức

Với bối cảnh UBND phường, xã, dữ liệu cuộc họp nên được quản lý theo `organization_id`. Mỗi cuộc họp thuộc một tổ chức hiện tại từ middleware `set.permissions.team` thông qua header `X-Organization-Id`.

Các thao tác xem, sửa, xóa, điểm danh, biểu quyết, chất vấn, ghi chú phải chặn truy cập chéo tổ chức.

---

## 4. Đề xuất module theo chuẩn dự án

Đề xuất tạo module mới: `Meeting`.

### 4.1 Cấu trúc thư mục đề xuất

```text
app/Modules/Meeting/
├── Controllers/
│   ├── MeetingController.php
│   ├── MeetingTypeController.php
│   ├── MeetingAttendeeGroupController.php
│   ├── MeetingAttendeeController.php
│   ├── MeetingDocumentController.php
│   ├── MeetingAttendanceController.php
│   ├── MeetingVoteController.php
│   ├── MeetingQuestionController.php
│   └── MeetingPersonalNoteController.php
├── Enums/
│   ├── MeetingStatusEnum.php
│   ├── MeetingTypeStatusEnum.php
│   ├── MeetingAttendeeGroupStatusEnum.php
│   ├── MeetingAttendeeStatusEnum.php
│   ├── MeetingAttendeeTypeEnum.php
│   ├── MeetingParticipantRoleEnum.php
│   ├── MeetingAttendanceStatusEnum.php
│   ├── MeetingDocumentStatusEnum.php
│   ├── MeetingVoteStatusEnum.php
│   ├── MeetingVoteOptionEnum.php
│   ├── MeetingQuestionStatusEnum.php
│   └── MeetingNotificationTypeEnum.php
├── Models/
│   ├── Meeting.php
│   ├── MeetingType.php
│   ├── MeetingAttendeeGroup.php
│   ├── MeetingAttendee.php
│   ├── MeetingParticipant.php
│   ├── MeetingAgenda.php
│   ├── MeetingDocument.php
│   ├── MeetingAttendance.php
│   ├── MeetingVoteTopic.php
│   ├── MeetingVoteResponse.php
│   ├── MeetingQuestion.php
│   ├── MeetingPersonalNote.php
│   └── MeetingView.php
├── Requests/
├── Resources/
├── Services/
│   ├── MeetingService.php
│   ├── MeetingTypeService.php
│   ├── MeetingAttendeeGroupService.php
│   ├── MeetingAttendeeService.php
│   ├── MeetingDocumentService.php
│   ├── MeetingAttendanceService.php
│   ├── MeetingVoteService.php
│   ├── MeetingQuestionService.php
│   ├── MeetingPersonalNoteService.php
│   └── MeetingNotificationService.php
├── Exports/
├── Imports/
└── Routes/
    ├── meeting.php
    ├── meeting_type.php
    ├── meeting_attendee_group.php
    ├── meeting_attendee.php
    ├── meeting_document.php
    ├── meeting_attendance.php
    ├── meeting_vote.php
    ├── meeting_question.php
    └── meeting_personal_note.php
```

### 4.2 Resource chính và resource con

- `MeetingType`: danh mục loại cuộc họp.
- `MeetingAttendeeGroup`: danh mục nhóm người dự họp riêng của module.
- `MeetingAttendee`: danh bạ người dự họp riêng của module, liên kết tài khoản lõi khi có.
- `Meeting`: thông tin cuộc họp.
- `MeetingParticipant`: danh sách người được mời vào từng cuộc họp cụ thể.
- `MeetingAgenda`: chương trình/nội dung cuộc họp.
- `MeetingDocument`: tài liệu cuộc họp.
- `MeetingAttendance`: điểm danh cuộc họp.
- `MeetingVoteTopic`: nội dung/chương trình biểu quyết.
- `MeetingVoteResponse`: phiếu biểu quyết của từng người.
- `MeetingQuestion`: đăng ký thảo luận/chất vấn.
- `MeetingPersonalNote`: ghi chú cá nhân.
- `MeetingView`: lịch sử lượt xem cuộc họp/tài liệu.

---

## 5. Thiết kế dữ liệu đề xuất

### 5.1 Bảng `meeting_types`

Bảng danh mục loại cuộc họp.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `organization_id` | bigint nullable | Tổ chức sở hữu nếu danh mục theo từng đơn vị |
| `name` | string | Tên loại cuộc họp |
| `code` | string nullable | Mã loại |
| `description` | text nullable | Mô tả |
| `status` | string | Trạng thái |
| `sort_order` | integer | Thứ tự hiển thị |
| `created_by` | bigint nullable | Người tạo |
| `updated_by` | bigint nullable | Người cập nhật |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

### 5.2 Bảng `meeting_attendee_groups`

Bảng danh mục nhóm người dự họp riêng của module họp không giấy.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `organization_id` | bigint | Tổ chức sở hữu |
| `name` | string | Tên nhóm người dự họp |
| `code` | string nullable | Mã nhóm |
| `description` | text nullable | Mô tả |
| `status` | string | Trạng thái |
| `sort_order` | integer | Thứ tự hiển thị |
| `created_by` | bigint nullable | Người tạo |
| `updated_by` | bigint nullable | Người cập nhật |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Ví dụ nhóm:

- Thường trực UBND.
- Đại biểu HĐND.
- Lãnh đạo phòng ban.
- Bí thư/Trưởng thôn/Tổ trưởng dân phố.
- Thư ký cuộc họp.
- Khách mời thường xuyên.

### 5.3 Bảng `meeting_attendees`

Bảng danh bạ người dự họp riêng cho module. Bảng này liên kết tới tài khoản hệ thống lõi qua `user_id`, nhưng vẫn lưu thông tin nghiệp vụ riêng để phục vụ mời họp, phân nhóm, điểm danh và biểu quyết.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `organization_id` | bigint | Tổ chức sở hữu |
| `meeting_attendee_group_id` | bigint nullable | Nhóm người dự họp |
| `user_id` | bigint nullable | Tài khoản hệ thống lõi nếu là người dùng nội bộ |
| `name` | string | Họ tên hiển thị trong module họp |
| `email` | string nullable | Email liên hệ |
| `phone` | string nullable | Số điện thoại |
| `position_name` | string nullable | Chức vụ/chức danh hiển thị |
| `department_name` | string nullable | Đơn vị/phòng ban hiển thị |
| `attendee_type` | string | Loại người dự họp |
| `status` | string | Trạng thái |
| `sort_order` | integer | Thứ tự hiển thị |
| `note` | text nullable | Ghi chú |
| `created_by` | bigint nullable | Người tạo |
| `updated_by` | bigint nullable | Người cập nhật |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Loại người dự họp gợi ý:

- `internal`: người dùng nội bộ có tài khoản hệ thống.
- `external`: khách mời ngoài hệ thống.
- `delegate`: đại biểu/đại diện theo kỳ họp.
- `staff`: cán bộ hỗ trợ/thư ký.

Lưu ý thiết kế:

- `user_id` nullable để vẫn mời được khách ngoài hệ thống khi chưa cấp tài khoản.
- Nếu `user_id` có giá trị, nên ràng buộc không trùng `organization_id + user_id` trong danh bạ người dự họp đang hoạt động.
- Thông tin `name`, `position_name`, `department_name` là thông tin nghiệp vụ của module và có thể khác hồ sơ tài khoản lõi tại từng thời điểm.

### 5.4 Bảng `meetings`

Bảng chính quản lý cuộc họp/kỳ họp.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `organization_id` | bigint | Tổ chức sở hữu |
| `meeting_type_id` | bigint | Loại cuộc họp |
| `title` | string | Tiêu đề cuộc họp |
| `description` | text nullable | Mô tả |
| `start_time` | datetime | Thời gian bắt đầu |
| `end_time` | datetime nullable | Thời gian kết thúc |
| `location` | string nullable | Địa chỉ/phòng họp |
| `chairperson_attendee_id` | bigint nullable | Người chủ trì từ danh bạ người dự họp |
| `moderator_attendee_id` | bigint nullable | Người điều hành từ danh bạ người dự họp |
| `secretary_attendee_id` | bigint nullable | Thư ký cuộc họp từ danh bạ người dự họp |
| `status` | string | Trạng thái cuộc họp |
| `view_count` | unsigned integer | Tổng lượt xem |
| `is_public` | boolean | Có công khai cuộc họp trên trang ngoài hay không |
| `allow_guest` | boolean | Có cho khách mời hay không |
| `is_online` | boolean | Có họp trực tuyến hay không |
| `online_url` | string nullable | Link họp trực tuyến |
| `published_at` | datetime nullable | Thời điểm phát hành |
| `cancelled_at` | datetime nullable | Thời điểm hủy |
| `cancel_reason` | text nullable | Lý do hủy |
| `created_by` | bigint nullable | Người tạo |
| `updated_by` | bigint nullable | Người cập nhật |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Trạng thái gợi ý:

- `draft`: bản nháp.
- `published`: đã phát hành/thông báo.
- `in_progress`: đang diễn ra.
- `completed`: đã kết thúc.
- `cancelled`: đã hủy.
- `archived`: đã lưu trữ.

Lưu ý công khai:

- `is_public = true` chỉ cho phép hiển thị thông tin cuộc họp ở trang công khai.
- Các dữ liệu nội bộ như danh sách người tham dự chi tiết, điểm danh, biểu quyết, chất vấn, ghi chú cá nhân không được đưa ra API công khai.
- Tài liệu của cuộc họp không tự động công khai theo cuộc họp; từng tài liệu phải bật công khai riêng.

### 5.5 Bảng `meeting_participants`

Bảng danh sách người được mời vào từng cuộc họp cụ thể. Dữ liệu chính lấy từ `meeting_attendees`, đồng thời lưu snapshot chức vụ/đơn vị để biên bản và báo cáo không bị thay đổi khi danh bạ cập nhật sau này.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `meeting_id` | bigint | Cuộc họp |
| `meeting_attendee_id` | bigint | Người dự họp trong danh bạ module |
| `role` | string | Vai trò trong cuộc họp |
| `display_name` | string | Họ tên snapshot tại thời điểm mời |
| `position_name` | string nullable | Chức vụ hiển thị tại thời điểm mời |
| `department_name` | string nullable | Đơn vị/phòng ban hiển thị tại thời điểm mời |
| `email` | string nullable | Email snapshot |
| `phone` | string nullable | Số điện thoại snapshot |
| `is_required` | boolean | Có bắt buộc tham dự hay không |
| `invitation_status` | string | Trạng thái lời mời |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Vai trò gợi ý:

- `chairperson`: người chủ trì.
- `moderator`: người điều hành.
- `secretary`: thư ký.
- `delegate`: đại biểu/người tham dự.
- `guest`: khách mời.

### 5.6 Bảng `meeting_agendas`

Bảng chương trình/nội dung cuộc họp.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `meeting_id` | bigint | Cuộc họp |
| `title` | string | Tên nội dung chương trình |
| `description` | text nullable | Mô tả |
| `presenter_attendee_id` | bigint nullable | Người trình bày từ danh bạ người dự họp |
| `start_time` | datetime nullable | Thời gian bắt đầu dự kiến |
| `end_time` | datetime nullable | Thời gian kết thúc dự kiến |
| `sort_order` | integer | Thứ tự |
| `status` | string | Trạng thái |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

### 5.7 Bảng `meeting_documents`

Bảng quản lý tài liệu cuộc họp. Tệp vật lý nên đi qua `Core\Services\MediaService`.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `meeting_id` | bigint | Cuộc họp |
| `meeting_agenda_id` | bigint nullable | Chương trình liên quan |
| `title` | string | Tên tài liệu |
| `description` | text nullable | Mô tả |
| `media_id` | bigint nullable | Media/tệp đính kèm |
| `document_type` | string nullable | Loại tài liệu |
| `status` | string | Trạng thái |
| `is_confidential` | boolean | Tài liệu mật/nội bộ |
| `is_public` | boolean | Có công khai tài liệu trên trang ngoài hay không |
| `view_count` | unsigned integer | Lượt xem |
| `download_count` | unsigned integer | Lượt tải |
| `sort_order` | integer | Thứ tự hiển thị |
| `published_at` | datetime nullable | Thời điểm phát hành |
| `created_by` | bigint nullable | Người tạo |
| `updated_by` | bigint nullable | Người cập nhật |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Lưu ý công khai tài liệu:

- Tài liệu chỉ hiển thị công khai khi cuộc họp cha `is_public = true`, tài liệu `is_public = true`, trạng thái tài liệu đã phát hành và `is_confidential = false`.
- Tài liệu mật không được phép bật công khai, kể cả khi cuộc họp cha đang công khai.

### 5.8 Bảng `meeting_attendances`

Bảng điểm danh theo từng người tham dự.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `meeting_id` | bigint | Cuộc họp |
| `meeting_participant_id` | bigint | Người tham dự |
| `status` | string | Trạng thái điểm danh |
| `checked_in_at` | datetime nullable | Thời điểm điểm danh |
| `checked_in_by` | bigint nullable | Người thực hiện điểm danh |
| `checkin_method` | string nullable | Cách điểm danh |
| `note` | text nullable | Ghi chú |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Trạng thái gợi ý:

- `pending`: chưa điểm danh.
- `present`: có mặt.
- `absent`: vắng mặt.
- `late`: đến muộn.
- `excused`: xin vắng.

### 5.9 Bảng `meeting_vote_topics`

Bảng chương trình/nội dung biểu quyết.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `meeting_id` | bigint | Cuộc họp |
| `meeting_agenda_id` | bigint nullable | Chương trình liên quan |
| `title` | string | Tiêu đề biểu quyết |
| `description` | text nullable | Nội dung biểu quyết |
| `status` | string | Trạng thái |
| `vote_mode` | string | Kiểu biểu quyết |
| `is_anonymous` | boolean | Có ẩn danh hay không |
| `opened_at` | datetime nullable | Thời điểm mở biểu quyết |
| `closed_at` | datetime nullable | Thời điểm đóng biểu quyết |
| `sort_order` | integer | Thứ tự |
| `created_by` | bigint nullable | Người tạo |
| `updated_by` | bigint nullable | Người cập nhật |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Trạng thái gợi ý:

- `draft`: bản nháp.
- `opened`: đang mở biểu quyết.
- `closed`: đã đóng biểu quyết.
- `cancelled`: đã hủy.

### 5.10 Bảng `meeting_vote_responses`

Bảng lưu phiếu biểu quyết của từng người.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `meeting_vote_topic_id` | bigint | Nội dung biểu quyết |
| `meeting_participant_id` | bigint | Người biểu quyết |
| `option` | string | Lựa chọn |
| `comment` | text nullable | Ý kiến kèm theo |
| `voted_at` | datetime | Thời điểm biểu quyết |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Ràng buộc quan trọng: mỗi `meeting_participant_id` chỉ có một phiếu trên mỗi `meeting_vote_topic_id`, trừ khi nghiệp vụ cho phép sửa phiếu trước khi đóng.

### 5.11 Bảng `meeting_questions`

Bảng đăng ký thảo luận/chất vấn.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `meeting_id` | bigint | Cuộc họp |
| `meeting_agenda_id` | bigint nullable | Chương trình liên quan |
| `meeting_participant_id` | bigint | Người đăng ký |
| `title` | string nullable | Tiêu đề ngắn |
| `content` | text | Nội dung thảo luận/chất vấn |
| `field` | string nullable | Lĩnh vực |
| `status` | string | Trạng thái |
| `approved_by` | bigint nullable | Người duyệt |
| `approved_at` | datetime nullable | Thời điểm duyệt |
| `answered_by` | bigint nullable | Người trả lời |
| `answer_content` | text nullable | Nội dung trả lời |
| `answered_at` | datetime nullable | Thời điểm trả lời |
| `sort_order` | integer | Thứ tự phát biểu |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Trạng thái gợi ý:

- `registered`: đã đăng ký.
- `approved`: đã duyệt.
- `rejected`: từ chối.
- `speaking`: đang phát biểu.
- `answered`: đã trả lời.
- `closed`: đã đóng.

### 5.12 Bảng `meeting_personal_notes`

Bảng ghi chú cá nhân.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `meeting_id` | bigint | Cuộc họp |
| `meeting_agenda_id` | bigint nullable | Chương trình liên quan |
| `meeting_document_id` | bigint nullable | Tài liệu liên quan |
| `meeting_attendee_id` | bigint nullable | Người dự họp tương ứng nếu có |
| `user_id` | bigint | Người ghi chú |
| `content` | longText | Nội dung ghi chú |
| `created_at` | timestamp | Ngày tạo |
| `updated_at` | timestamp | Ngày cập nhật |

Ghi chú cá nhân cần luôn scope theo `user_id`; quản trị viên không nên xem nội dung ghi chú riêng nếu không có chính sách pháp lý rõ ràng.

### 5.13 Bảng `meeting_views`

Bảng theo dõi lượt xem.

| Trường | Kiểu | Mô tả |
| --- | --- | --- |
| `id` | bigint | Khóa chính |
| `meeting_id` | bigint | Cuộc họp |
| `meeting_document_id` | bigint nullable | Tài liệu nếu lượt xem tài liệu |
| `user_id` | bigint nullable | Người xem |
| `ip_address` | string nullable | IP |
| `user_agent` | text nullable | Thiết bị/trình duyệt |
| `viewed_at` | datetime | Thời điểm xem |

Có thể dùng bảng này để tính chi tiết, đồng thời denormalize `view_count` ở `meetings` và `meeting_documents` để tối ưu danh sách.

---

## 6. Endpoint API đề xuất

### 6.1 Endpoint chuẩn cho loại cuộc họp

Áp dụng đủ bộ action chuẩn dự án:

- `GET /api/meeting-types/stats`
- `GET /api/meeting-types`
- `GET /api/meeting-types/{meetingType}`
- `POST /api/meeting-types`
- `PUT /api/meeting-types/{meetingType}`
- `DELETE /api/meeting-types/{meetingType}`
- `POST /api/meeting-types/bulk-delete`
- `POST /api/meeting-types/bulk-update-status`
- `PATCH /api/meeting-types/{meetingType}/change-status`
- `GET /api/meeting-types/export`
- `POST /api/meeting-types/import`
- `GET /api/meeting-types/public`
- `GET /api/meeting-types/public-options`

### 6.2 Endpoint chuẩn cho nhóm người dự họp

- `GET /api/meeting-attendee-groups/stats`
- `GET /api/meeting-attendee-groups`
- `GET /api/meeting-attendee-groups/{attendeeGroup}`
- `POST /api/meeting-attendee-groups`
- `PUT /api/meeting-attendee-groups/{attendeeGroup}`
- `DELETE /api/meeting-attendee-groups/{attendeeGroup}`
- `POST /api/meeting-attendee-groups/bulk-delete`
- `POST /api/meeting-attendee-groups/bulk-update-status`
- `PATCH /api/meeting-attendee-groups/{attendeeGroup}/change-status`
- `GET /api/meeting-attendee-groups/export`
- `POST /api/meeting-attendee-groups/import`
- `GET /api/meeting-attendee-groups/public`
- `GET /api/meeting-attendee-groups/public-options`

### 6.3 Endpoint chuẩn cho người dự họp

- `GET /api/meeting-attendees/stats`
- `GET /api/meeting-attendees`
- `GET /api/meeting-attendees/{attendee}`
- `POST /api/meeting-attendees`
- `PUT /api/meeting-attendees/{attendee}`
- `DELETE /api/meeting-attendees/{attendee}`
- `POST /api/meeting-attendees/bulk-delete`
- `POST /api/meeting-attendees/bulk-update-status`
- `PATCH /api/meeting-attendees/{attendee}/change-status`
- `GET /api/meeting-attendees/export`
- `POST /api/meeting-attendees/import`
- `GET /api/meeting-attendees/public-options`
- `GET /api/meeting-attendee-groups/{attendeeGroup}/attendees`

### 6.4 Endpoint chuẩn cho cuộc họp

- `GET /api/meetings/stats`
- `GET /api/meetings`
- `GET /api/meetings/calendar`
- `GET /api/meetings/{meeting}`
- `POST /api/meetings`
- `PUT /api/meetings/{meeting}`
- `DELETE /api/meetings/{meeting}`
- `POST /api/meetings/bulk-delete`
- `POST /api/meetings/bulk-update-status`
- `PATCH /api/meetings/{meeting}/change-status`
- `POST /api/meetings/{meeting}/publish`
- `PATCH /api/meetings/{meeting}/change-public-status`
- `POST /api/meetings/{meeting}/start`
- `POST /api/meetings/{meeting}/complete`
- `POST /api/meetings/{meeting}/cancel`
- `POST /api/meetings/{meeting}/duplicate`
- `POST /api/meetings/{meeting}/increment-view`
- `GET /api/meetings/export`
- `POST /api/meetings/import`

### 6.5 Endpoint công khai cho trang chủ họp không giấy

Các endpoint này đặt ngoài nhóm `auth:sanctum` và phải có PHPDoc `@unauthenticated` khi triển khai Scribe.

- `GET /api/public/meetings`
- `GET /api/public/meetings/{meeting}`
- `GET /api/public/meetings/{meeting}/documents`
- `GET /api/public/meetings/{meeting}/documents/{document}`
- `GET /api/public/meetings/{meeting}/documents/{document}/download`

Nguyên tắc trả dữ liệu:

- `GET /api/public/meetings`: chỉ trả các cuộc họp `is_public = true`, trạng thái phù hợp để công khai, thuộc tổ chức/portal công khai hiện tại.
- `GET /api/public/meetings/{meeting}`: chỉ trả thông tin được phép công khai như tiêu đề, mô tả, thời gian, địa điểm, loại cuộc họp, chương trình công khai nếu có.
- `GET /api/public/meetings/{meeting}/documents`: chỉ trả tài liệu thỏa điều kiện cuộc họp công khai, tài liệu công khai, đã phát hành, không mật.
- Endpoint tải tài liệu phải kiểm tra lại `meetings.is_public = true`, `meeting_documents.is_public = true`, tài liệu đã phát hành và không mật.
- Không trả dữ liệu điểm danh, biểu quyết cá nhân, danh sách người tham dự nội bộ, chất vấn nội bộ, ghi chú cá nhân.

### 6.6 Endpoint cho danh sách mời trong cuộc họp

- `GET /api/meetings/{meeting}/participants`
- `POST /api/meetings/{meeting}/participants`
- `POST /api/meetings/{meeting}/participants/add-from-group`
- `PATCH /api/meetings/{meeting}/participants/{participant}`
- `DELETE /api/meetings/{meeting}/participants/{participant}`
- `POST /api/meetings/{meeting}/participants/sync`

### 6.7 Endpoint cho tài liệu cuộc họp

- `GET /api/meetings/{meeting}/documents`
- `POST /api/meetings/{meeting}/documents`
- `GET /api/meetings/{meeting}/documents/{document}`
- `PUT /api/meetings/{meeting}/documents/{document}`
- `DELETE /api/meetings/{meeting}/documents/{document}`
- `POST /api/meetings/{meeting}/documents/{document}/publish`
- `POST /api/meetings/{meeting}/documents/{document}/revoke`
- `POST /api/meetings/{meeting}/documents/{document}/increment-view`
- `GET /api/meetings/{meeting}/documents/{document}/download`
- `PATCH /api/meetings/{meeting}/documents/{document}/change-public-status`

### 6.8 Endpoint cho điểm danh

- `GET /api/meetings/{meeting}/attendances`
- `POST /api/meetings/{meeting}/attendances/sync-from-participants`
- `PATCH /api/meetings/{meeting}/attendances/{attendance}`
- `POST /api/meetings/{meeting}/attendances/check-in`
- `POST /api/meetings/{meeting}/attendances/qr/check-in`
- `GET /api/meetings/{meeting}/attendances/export`

### 6.9 Endpoint cho biểu quyết

- `GET /api/meetings/{meeting}/vote-topics`
- `POST /api/meetings/{meeting}/vote-topics`
- `GET /api/meetings/{meeting}/vote-topics/{voteTopic}`
- `PUT /api/meetings/{meeting}/vote-topics/{voteTopic}`
- `DELETE /api/meetings/{meeting}/vote-topics/{voteTopic}`
- `POST /api/meetings/{meeting}/vote-topics/{voteTopic}/open`
- `POST /api/meetings/{meeting}/vote-topics/{voteTopic}/close`
- `POST /api/meetings/{meeting}/vote-topics/{voteTopic}/vote`
- `GET /api/meetings/{meeting}/vote-topics/{voteTopic}/results`
- `GET /api/meetings/{meeting}/vote-topics/{voteTopic}/export`

### 6.10 Endpoint cho thảo luận/chất vấn

- `GET /api/meetings/{meeting}/questions`
- `POST /api/meetings/{meeting}/questions`
- `GET /api/meetings/{meeting}/questions/{question}`
- `PUT /api/meetings/{meeting}/questions/{question}`
- `DELETE /api/meetings/{meeting}/questions/{question}`
- `POST /api/meetings/{meeting}/questions/{question}/approve`
- `POST /api/meetings/{meeting}/questions/{question}/reject`
- `POST /api/meetings/{meeting}/questions/{question}/answer`
- `PATCH /api/meetings/{meeting}/questions/reorder`
- `GET /api/meetings/{meeting}/questions/export`

### 6.11 Endpoint cho ghi chú cá nhân

- `GET /api/meetings/{meeting}/personal-notes`
- `POST /api/meetings/{meeting}/personal-notes`
- `PUT /api/meetings/{meeting}/personal-notes/{note}`
- `DELETE /api/meetings/{meeting}/personal-notes/{note}`

---

## 7. Bộ lọc, sắp xếp và thống kê

### 7.1 Bộ lọc danh sách cuộc họp

Danh sách `index` của cuộc họp nên hỗ trợ:

- `search`: tìm theo tiêu đề, mô tả, địa điểm.
- `status`: lọc theo trạng thái.
- `meeting_type_id`: lọc theo loại cuộc họp.
- `chairperson_attendee_id`: lọc theo người chủ trì trong danh bạ người dự họp.
- `moderator_attendee_id`: lọc theo người điều hành trong danh bạ người dự họp.
- `is_public`: lọc theo chế độ công khai.
- `from_date`, `to_date`: lọc theo khoảng thời gian bắt đầu.
- `created_from`, `created_to`: lọc theo khoảng ngày tạo.
- `sort_by`: `id`, `title`, `start_time`, `end_time`, `created_at`, `updated_at`, `view_count`.
- `sort_order`: `asc` hoặc `desc`.
- `limit`: số lượng bản ghi mỗi trang.

### 7.2 Thống kê cuộc họp

Endpoint `stats` nên trả về:

- Tổng số cuộc họp.
- Số cuộc họp theo trạng thái.
- Số cuộc họp theo loại.
- Số cuộc họp trong ngày/tuần/tháng.
- Số cuộc họp sắp diễn ra.
- Số cuộc họp đang diễn ra.
- Số cuộc họp đã hủy.
- Tổng số lượt xem.
- Số cuộc họp đang công khai.
- Số tài liệu đang công khai.
- Tỷ lệ điểm danh trung bình.
- Tỷ lệ biểu quyết trung bình.

### 7.3 Thống kê chi tiết trong một cuộc họp

Trang chi tiết cuộc họp nên có khối tổng hợp:

- Tổng số người được mời.
- Số người đã xác nhận/có mặt/vắng mặt.
- Số tài liệu đã phát hành.
- Tổng lượt xem tài liệu.
- Số nội dung biểu quyết đang mở/đã đóng.
- Số người đã biểu quyết trên từng nội dung.
- Số câu hỏi/chất vấn theo trạng thái.

---

## 8. Luồng nghiệp vụ chính

### 8.1 Luồng chuẩn bị cuộc họp

1. Thư ký tạo cuộc họp ở trạng thái `draft`.
2. Chọn loại cuộc họp, thời gian, địa điểm, người chủ trì, người điều hành.
3. Thêm danh sách người tham dự từ danh bạ người dự họp hoặc chọn nhanh theo nhóm người dự họp.
4. Tạo chương trình cuộc họp.
5. Tải lên tài liệu, gắn với cuộc họp hoặc từng chương trình.
6. Kiểm tra quyền xem tài liệu nếu có tài liệu mật.
7. Phát hành cuộc họp.
8. Hệ thống gửi thông báo lịch họp đến người tham dự.

### 8.2 Luồng trước cuộc họp

1. Người tham dự nhận thông báo.
2. Người tham dự xem thông tin cuộc họp và tài liệu.
3. Hệ thống ghi nhận lượt xem.
4. Hệ thống gửi nhắc lịch trước cuộc họp theo mốc cấu hình.
5. Thư ký theo dõi danh sách người chưa xem tài liệu hoặc chưa xác nhận.

### 8.3 Luồng trong cuộc họp

1. Thư ký chuyển cuộc họp sang trạng thái `in_progress`.
2. Người tham dự điểm danh hoặc thư ký điểm danh thủ công.
3. Người điều hành theo dõi chương trình.
4. Người tham dự gửi đăng ký thảo luận/chất vấn.
5. Người điều hành mở biểu quyết khi đến nội dung cần biểu quyết.
6. Người tham dự biểu quyết trên thiết bị cá nhân.
7. Kết quả biểu quyết được cập nhật thời gian thực.
8. Thư ký ghi nhận câu trả lời/chốt nội dung.

### 8.4 Luồng sau cuộc họp

1. Thư ký đóng các nội dung biểu quyết còn mở.
2. Tổng hợp điểm danh, biểu quyết, chất vấn.
3. Kết thúc cuộc họp.
4. Xuất báo cáo hoặc biên bản dữ liệu.
5. Lưu trữ tài liệu, kết quả và lịch sử.

---

## 9. Thời gian thực và thông báo

### 9.1 Tính năng nên realtime

- Trạng thái cuộc họp: bắt đầu, kết thúc, hủy.
- Mở/đóng biểu quyết.
- Kết quả biểu quyết tổng hợp.
- Danh sách người đã/chưa biểu quyết.
- Danh sách đăng ký thảo luận/chất vấn.
- Trạng thái điểm danh.
- Thông báo tài liệu mới hoặc tài liệu bị thu hồi.

### 9.2 Kênh realtime đề xuất

- `meeting.{meeting_id}`: kênh chung của cuộc họp.
- `meeting.{meeting_id}.attendance`: cập nhật điểm danh.
- `meeting.{meeting_id}.votes`: cập nhật biểu quyết.
- `meeting.{meeting_id}.questions`: cập nhật thảo luận/chất vấn.
- `user.{user_id}.notifications`: thông báo cá nhân.

Nếu triển khai trong Laravel, có thể dùng broadcasting với Pusher-compatible server, Laravel Reverb hoặc WebSocket server nội bộ.

### 9.3 Thông báo và nhắc lịch

Các loại thông báo chính:

- `meeting_created`: có lịch họp mới.
- `meeting_updated`: lịch họp thay đổi.
- `meeting_cancelled`: cuộc họp bị hủy.
- `meeting_reminder`: nhắc lịch họp.
- `document_published`: có tài liệu mới.
- `document_revoked`: tài liệu bị thu hồi.
- `vote_opened`: mở biểu quyết.
- `vote_closed`: đóng biểu quyết.
- `question_approved`: câu hỏi/chất vấn được duyệt.

Kênh gửi:

- Thông báo trong hệ thống.
- Email nếu có cấu hình.
- SMS/Zalo OA nếu tích hợp bên thứ ba.
- Push notification nếu có ứng dụng di động.

Mốc nhắc lịch gợi ý:

- Trước 24 giờ.
- Trước 2 giờ.
- Trước 15 phút.
- Khi có thay đổi quan trọng về thời gian/địa điểm/tài liệu.

---

## 10. Quyền và phân quyền

### 10.1 Permission đề xuất

Theo chuẩn `{resource}.{action}`, có thể bổ sung:

```text
meeting-types.view
meeting-types.create
meeting-types.update
meeting-types.delete
meeting-types.export
meeting-types.import

meeting-attendee-groups.view
meeting-attendee-groups.create
meeting-attendee-groups.update
meeting-attendee-groups.delete
meeting-attendee-groups.export
meeting-attendee-groups.import

meeting-attendees.view
meeting-attendees.create
meeting-attendees.update
meeting-attendees.delete
meeting-attendees.export
meeting-attendees.import

meetings.view
meetings.create
meetings.update
meetings.delete
meetings.publish
meetings.cancel
meetings.start
meetings.complete
meetings.duplicate
meetings.public
meetings.export
meetings.import

meeting-participants.view
meeting-participants.create
meeting-participants.update
meeting-participants.delete
meeting-participants.sync

meeting-documents.view
meeting-documents.create
meeting-documents.update
meeting-documents.delete
meeting-documents.publish
meeting-documents.revoke
meeting-documents.public
meeting-documents.download

meeting-attendances.view
meeting-attendances.update
meeting-attendances.export

meeting-votes.view
meeting-votes.create
meeting-votes.update
meeting-votes.delete
meeting-votes.open
meeting-votes.close
meeting-votes.vote
meeting-votes.export

meeting-questions.view
meeting-questions.create
meeting-questions.update
meeting-questions.delete
meeting-questions.approve
meeting-questions.answer
meeting-questions.export

meeting-personal-notes.view
meeting-personal-notes.create
meeting-personal-notes.update
meeting-personal-notes.delete
```

Khi triển khai cần cập nhật `database/seeders/PermissionSeeder.php` và `app/Modules/Core/Middleware/LogActivity.php`.

### 10.2 Chính sách truy cập quan trọng

- Người tạo/thư ký có thể sửa cuộc họp khi còn `draft`.
- Sau khi `published`, chỉ một số trường được phép sửa; thay đổi quan trọng phải phát thông báo.
- Bật/tắt công khai cuộc họp và tài liệu nên tách quyền riêng, không gộp mặc định vào quyền cập nhật.
- Cuộc họp chỉ được công khai khi đã ở trạng thái phù hợp như `published`, `in_progress` hoặc `completed`; không công khai bản nháp/họp đã hủy nếu không có nhu cầu đặc biệt.
- Tài liệu chỉ được công khai khi tài liệu đã phát hành, không mật và cuộc họp cha đang được công khai.
- Chỉ người có quyền quản trị module mới được tạo/sửa/xóa nhóm người dự họp và danh bạ người dự họp.
- Người dự họp nội bộ phải liên kết đúng tài khoản lõi thuộc phạm vi tổ chức hiện tại.
- Người tham dự chỉ xem cuộc họp mà mình được mời hoặc có quyền quản trị.
- Tài liệu mật chỉ hiển thị với nhóm người được cấp quyền.
- Người dùng chỉ xem/sửa/xóa ghi chú cá nhân của chính mình.
- Người tham dự chỉ biểu quyết khi thuộc danh sách hợp lệ và nội dung biểu quyết đang mở.
- Kết quả biểu quyết đã đóng không được sửa nếu không có quyền quản trị đặc biệt.

---

## 11. Import, export và báo cáo

### 11.1 Export nhóm người dự họp

Các trường nên xuất:

- `id`
- `name`
- `code`
- `description`
- `status`
- `sort_order`
- `created_by`
- `updated_by`
- `created_at`
- `updated_at`

### 11.2 Import nhóm người dự họp

Cột bắt buộc:

- `name`

Cột không bắt buộc:

- `code`
- `description`
- `status` mặc định `active`
- `sort_order` mặc định `0`

### 11.3 Export người dự họp

Các trường nên xuất:

- `id`
- `name`
- `meeting_attendee_group`
- `user`
- `email`
- `phone`
- `position_name`
- `department_name`
- `attendee_type`
- `status`
- `sort_order`
- `created_by`
- `updated_by`
- `created_at`
- `updated_at`

### 11.4 Import người dự họp

Cột bắt buộc:

- `name`

Cột không bắt buộc:

- `meeting_attendee_group`
- `user_email`
- `email`
- `phone`
- `position_name`
- `department_name`
- `attendee_type` mặc định `internal` nếu khớp `user_email`, ngược lại mặc định `external`
- `status` mặc định `active`
- `sort_order` mặc định `0`

Khi import người dự họp, nếu có `user_email` thì service nên tìm tài khoản hệ thống lõi tương ứng trong phạm vi tổ chức hiện tại để gán `user_id`. Nếu không tìm thấy, có thể báo lỗi hoặc tạo bản ghi người dự họp ngoài hệ thống tùy cấu hình import.

### 11.5 Export danh sách cuộc họp

Các trường nên xuất:

- `id`
- `title`
- `meeting_type`
- `description`
- `start_time`
- `end_time`
- `location`
- `chairperson_attendee`
- `moderator_attendee`
- `secretary_attendee`
- `participant_count`
- `document_count`
- `agenda_count`
- `status`
- `view_count`
- `created_by`
- `updated_by`
- `created_at`
- `updated_at`

### 11.6 Import cuộc họp

Cột bắt buộc:

- `title`
- `meeting_type`
- `start_time`

Cột không bắt buộc:

- `description`
- `end_time`
- `location`
- `chairperson_attendee`
- `moderator_attendee`
- `secretary_attendee`
- `status` mặc định `draft`
- `participants` mặc định rỗng

File import cần validate `required|file|mimes:xlsx,xls,csv|max:10240`.

### 11.7 Báo cáo sau cuộc họp

Báo cáo cuộc họp nên gồm:

- Thông tin chung cuộc họp.
- Danh sách người tham dự, vắng mặt, xin vắng.
- Danh sách tài liệu đã phát hành.
- Kết quả từng nội dung biểu quyết.
- Danh sách thảo luận/chất vấn và câu trả lời.
- Tổng hợp lượt xem tài liệu.
- Thời gian bắt đầu/kết thúc thực tế.

---

## 12. Giao diện đề xuất

### 12.1 Trang chủ công khai họp không giấy

Trang công khai phục vụ người dân, đại biểu, khách truy cập hoặc các đối tượng không đăng nhập xem những nội dung được phép công khai.

- Danh sách cuộc họp/kỳ họp công khai.
- Bộ lọc theo loại cuộc họp, thời gian, từ khóa, trạng thái công khai.
- Trang chi tiết cuộc họp công khai gồm tiêu đề, tóm tắt, thời gian, địa điểm, loại cuộc họp, chương trình được phép công khai.
- Danh sách tài liệu công khai thuộc cuộc họp.
- Xem hoặc tải tài liệu nếu tài liệu cho phép tải công khai.
- Không hiển thị dữ liệu nội bộ như điểm danh, biểu quyết cá nhân, danh sách người tham dự nội bộ, ghi chú cá nhân.

### 12.2 Trang danh sách cuộc họp quản trị

- Bộ lọc nhanh theo trạng thái: sắp diễn ra, đang diễn ra, đã kết thúc, đã hủy.
- Bộ lọc theo loại cuộc họp, người chủ trì, khoảng ngày, chế độ công khai.
- Hiển thị dạng bảng và dạng lịch.
- Badge trạng thái, công khai/nội bộ, thời gian, địa điểm, số người tham dự, số tài liệu.
- Thao tác bật/tắt công khai cuộc họp nếu có quyền.

### 12.3 Trang danh bạ người dự họp

- Quản lý nhóm người dự họp.
- Quản lý người dự họp theo nhóm, chức vụ, đơn vị, loại người dự họp.
- Liên kết người dự họp nội bộ với tài khoản hệ thống lõi.
- Chọn nhanh người dự họp khi tạo cuộc họp hoặc thêm từ cả nhóm.

### 12.4 Trang chi tiết cuộc họp

Nên chia thành các tab:

- **Tổng quan:** thông tin chính, trạng thái, thống kê nhanh.
- **Chương trình:** danh sách nội dung họp.
- **Tài liệu:** tài liệu theo cuộc họp/chương trình, có trạng thái công khai riêng từng tài liệu.
- **Người tham dự:** danh sách mời và vai trò.
- **Điểm danh:** trạng thái tham dự.
- **Biểu quyết:** danh sách nội dung và kết quả.
- **Chất vấn:** đăng ký thảo luận, câu hỏi, trả lời.
- **Ghi chú cá nhân:** ghi chú riêng của người dùng.
- **Lịch sử:** log thay đổi và hoạt động.

### 12.5 Màn hình điều hành cuộc họp

Màn hình riêng cho người điều hành/thư ký:

- Bắt đầu/kết thúc cuộc họp.
- Điều hướng chương trình.
- Theo dõi điểm danh.
- Mở/đóng biểu quyết.
- Xem kết quả biểu quyết thời gian thực.
- Duyệt và sắp xếp chất vấn.
- Ghi nhận kết luận nhanh.

### 12.6 Màn hình người tham dự

- Xem lịch họp của tôi.
- Xem tài liệu.
- Điểm danh.
- Biểu quyết.
- Đăng ký phát biểu/chất vấn.
- Ghi chú cá nhân.
- Nhận thông báo thay đổi.

---

## 13. Ràng buộc kỹ thuật và lưu ý triển khai

### 13.1 Transaction

Cần dùng `DB::transaction()` cho các luồng ghi nhiều bước:

- Tạo cuộc họp kèm người tham dự, chương trình, tài liệu.
- Cập nhật cuộc họp kèm sync người tham dự/chương trình.
- Thêm người tham dự từ nhóm người dự họp và tạo danh sách điểm danh tương ứng.
- Phát hành cuộc họp kèm tạo thông báo.
- Bật/tắt công khai cuộc họp kèm kiểm tra trạng thái và tạo log hoạt động.
- Bật/tắt công khai tài liệu kèm kiểm tra tài liệu không mật, đã phát hành và cuộc họp cha đang công khai.
- Tạo tài liệu kèm upload media.
- Mở/đóng biểu quyết kèm broadcast event.
- Kết thúc cuộc họp kèm đóng biểu quyết còn mở.

Không cần transaction cho thao tác đọc hoặc ghi một bảng đơn giản như tăng lượt xem, trừ khi có yêu cầu nhất quán cao hơn.

### 13.2 Media

Tất cả upload/xóa tài liệu họp nên đi qua `App\Modules\Core\Services\MediaService`. Không nên gọi trực tiếp `addMedia()` hoặc `Storage::put/delete` trong service của module `Meeting`.

Nếu upload file nằm trong transaction, cần có cơ chế cleanup file khi transaction lỗi.

### 13.3 Multi-tenant

Các bảng nghiệp vụ chính cần scope theo tổ chức:

- `meetings`
- `meeting_types` nếu danh mục theo tổ chức
- `meeting_attendee_groups`
- `meeting_attendees`
- các bảng con thông qua `meeting_id`

Các thao tác theo ID phải kiểm tra cuộc họp hoặc danh bạ người dự họp thuộc `organization_id` hiện tại. Với bảng con, không chỉ kiểm tra ID bản ghi con mà phải kiểm tra quan hệ ngược về `meetings.organization_id`.

### 13.4 Hiệu năng

- Dùng eager loading cho danh sách cuộc họp: loại, người chủ trì, người điều hành, thư ký từ danh bạ người dự họp.
- Dùng counter cache cho `view_count`, `participant_count`, `document_count` nếu dữ liệu lớn.
- Với trang công khai, cần index `organization_id`, `is_public`, `start_time`, `status`.
- Với tài liệu công khai, cần index `meeting_id`, `is_public`, `is_confidential`, `status`.
- Với biểu quyết realtime, tránh query toàn bộ phiếu sau mỗi lượt; nên tính aggregate theo topic.
- Với tài liệu dung lượng lớn, dùng link tải có kiểm tra quyền thay vì expose đường dẫn public.
- Với lịch họp, cần index `organization_id`, `start_time`, `end_time`, `status`.

### 13.5 Bảo mật

- Kiểm soát quyền xem tài liệu theo người tham dự và tổ chức.
- API công khai chỉ được dùng Resource riêng, không tái sử dụng Resource quản trị nếu Resource quản trị chứa dữ liệu nội bộ.
- Không công khai tài liệu `is_confidential = true`.
- Không công khai danh sách điểm danh, phiếu biểu quyết cá nhân, câu hỏi nội bộ, ghi chú cá nhân và danh sách liên hệ người dự họp.
- Link tải tài liệu công khai phải kiểm tra lại trạng thái công khai tại thời điểm tải, không chỉ dựa vào URL file tĩnh.
- Nên ghi log thao tác bật/tắt công khai cuộc họp và tài liệu.
- Không cho người ngoài danh sách biểu quyết gửi phiếu.
- Không cho biểu quyết sau khi topic đã đóng.
- Ghi log các thao tác nhạy cảm: phát hành, hủy họp, thu hồi tài liệu, mở/đóng biểu quyết, sửa kết quả.
- Cân nhắc watermark tài liệu nếu có tài liệu mật.
- Cân nhắc mã hóa hoặc giới hạn truy cập ghi chú cá nhân.

---

## 14. Lộ trình triển khai đề xuất

### Giai đoạn 1: MVP quản lý cuộc họp không giấy

- Quản lý loại cuộc họp.
- Quản lý nhóm người dự họp.
- Quản lý người dự họp riêng của module, liên kết tài khoản lõi.
- Quản lý cuộc họp.
- Quản lý danh sách người tham dự theo từng cuộc họp.
- Quản lý chương trình cuộc họp.
- Quản lý tài liệu cuộc họp.
- Trang chủ công khai cuộc họp và tài liệu.
- Thông báo lịch họp cơ bản.
- Lượt xem cuộc họp/tài liệu.
- Export/import danh sách cuộc họp.

### Giai đoạn 2: Vận hành trong cuộc họp

- Điểm danh.
- Biểu quyết.
- Đăng ký thảo luận/chất vấn.
- Ghi chú cá nhân.
- Màn hình điều hành cuộc họp.
- Báo cáo sau cuộc họp.

### Giai đoạn 3: Realtime và tích hợp nâng cao

- Biểu quyết thời gian thực.
- Cập nhật điểm danh/chất vấn realtime.
- QR code điểm danh.
- Push notification.
- Tích hợp Zalo OA/SMS/email.
- Đồng bộ lịch cá nhân hoặc lịch cơ quan.

### Giai đoạn 4: Tối ưu quản trị và lưu trữ

- Dashboard thống kê.
- Lưu trữ hồ sơ cuộc họp.
- Tìm kiếm toàn văn tài liệu.
- Watermark tài liệu.
- Phân quyền chi tiết theo tài liệu/chương trình.
- Trích xuất biên bản và báo cáo tổng hợp.

---

## 15. Kết luận

Giải pháp quản lý kỳ họp không giấy nên được thiết kế quanh resource trung tâm `Meeting`, đồng thời có 2 danh mục riêng của module là `MeetingAttendeeGroup` và `MeetingAttendee` để quản lý nhóm người dự họp và danh bạ người dự họp dựa trên tài khoản hệ thống lõi. Từ danh bạ này, từng cuộc họp sinh ra `MeetingParticipant` làm danh sách người được mời cụ thể, phục vụ điểm danh, biểu quyết, chất vấn và báo cáo.

Với bối cảnh UBND phường, xã, trọng tâm triển khai không chỉ là CRUD cuộc họp mà còn là khả năng vận hành trong thời gian thực, kiểm soát tài liệu, minh bạch điểm danh/biểu quyết và thông báo kịp thời cho người tham dự. Nếu triển khai theo module `Meeting` độc lập, hệ thống có thể phát triển từng giai đoạn mà vẫn giữ được chuẩn modular, multi-tenant, service layer và response format hiện tại của dự án.
