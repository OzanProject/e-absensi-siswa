const docx = require("docx");
const fs = require("fs");
const path = require("path");

const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  WidthType, AlignmentType, HeadingLevel, BorderStyle, ShadingType,
  PageBreak, TableLayoutType, VerticalAlign, Header, Footer, PageNumber,
  NumberFormat,
} = docx;

// ======================== COLOR PALETTE ========================
const C = {
  primary: "1A5276",
  primaryLight: "2980B9",
  accent: "E67E22",
  success: "27AE60",
  danger: "E74C3C",
  dark: "2C3E50",
  gray: "7F8C8D",
  lightGray: "BDC3C7",
  bgLight: "F8F9FA",
  bgHeader: "1A5276",
  white: "FFFFFF",
  rowEven: "F2F4F5",
  rowOdd: "FFFFFF",
};

// ======================== HELPER FUNCTIONS ========================
const noBorder = { style: BorderStyle.NONE, size: 0, color: C.white };
const noBorders = { top: noBorder, bottom: noBorder, left: noBorder, right: noBorder };
const thinBorder = { style: BorderStyle.SINGLE, size: 1, color: C.lightGray };
const cellBorders = { top: thinBorder, bottom: thinBorder, left: thinBorder, right: thinBorder };

function headerCell(text, widthPct) {
  return new TableCell({
    children: [new Paragraph({
      children: [new TextRun({ text, bold: true, color: C.white, size: 20, font: "Calibri" })],
      alignment: AlignmentType.LEFT,
      spacing: { before: 40, after: 40 },
    })],
    shading: { type: ShadingType.CLEAR, fill: C.bgHeader },
    borders: cellBorders,
    width: { size: widthPct, type: WidthType.PERCENTAGE },
    verticalAlign: VerticalAlign.CENTER,
  });
}

function dataCell(text, widthPct, opts = {}) {
  const runs = [];
  if (opts.badge) {
    runs.push(new TextRun({
      text: ` ${opts.badge} `,
      bold: true,
      color: C.white,
      size: 16,
      font: "Calibri",
      shading: { type: ShadingType.CLEAR, fill: opts.badge === "PK" ? C.accent : opts.badge === "FK" ? C.primaryLight : C.success },
    }));
    runs.push(new TextRun({ text: " ", size: 20 }));
  }
  runs.push(new TextRun({
    text,
    bold: opts.bold || false,
    color: opts.color || C.dark,
    size: opts.size || 20,
    font: "Calibri",
    italics: opts.italic || false,
  }));
  if (opts.ref) {
    runs.push(new TextRun({ text: `  → ${opts.ref}`, color: C.danger, size: 18, font: "Calibri", italics: true }));
  }
  return new TableCell({
    children: [new Paragraph({ children: runs, alignment: opts.align || AlignmentType.LEFT, spacing: { before: 30, after: 30 } })],
    shading: opts.rowIdx !== undefined && opts.rowIdx % 2 === 0 ? { type: ShadingType.CLEAR, fill: C.rowEven } : undefined,
    borders: cellBorders,
    width: { size: widthPct, type: WidthType.PERCENTAGE },
    verticalAlign: VerticalAlign.CENTER,
  });
}

function tableFromData(headers, widths, rows) {
  const headerRow = new TableRow({
    children: headers.map((h, i) => headerCell(h, widths[i])),
    tableHeader: true,
  });
  const dataRows = rows.map((row, rowIdx) =>
    new TableRow({
      children: row.map((cell, i) => {
        if (typeof cell === "object" && cell._custom) {
          return dataCell(cell.text, widths[i], { ...cell, rowIdx });
        }
        return dataCell(String(cell), widths[i], { rowIdx });
      }),
    })
  );
  return new Table({
    rows: [headerRow, ...dataRows],
    width: { size: 100, type: WidthType.PERCENTAGE },
    layout: TableLayoutType.FIXED,
  });
}

function heading(text, level = HeadingLevel.HEADING_2) {
  return new Paragraph({
    children: [new TextRun({ text, bold: true, color: C.primary, font: "Calibri" })],
    heading: level,
    spacing: { before: 300, after: 120 },
  });
}

function para(text, opts = {}) {
  return new Paragraph({
    children: [new TextRun({ text, size: 22, font: "Calibri", color: C.dark, ...opts })],
    alignment: opts.align || AlignmentType.JUSTIFIED,
    spacing: { before: 60, after: 60 },
  });
}

function emptyLine() {
  return new Paragraph({ children: [], spacing: { before: 100, after: 100 } });
}

function pageBreak() {
  return new Paragraph({ children: [new PageBreak()] });
}

// ======================== TABLE DEFINITIONS ========================
const tables = [
  {
    name: "users", label: "Users (Pengguna Sistem)",
    desc: "Menyimpan data seluruh pengguna yang dapat mengakses sistem. Terdapat 6 jenis role: super_admin, wali_kelas, orang_tua, guru, siswa, dan kepala_sekolah.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "name", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Nama lengkap pengguna" },
      { no: 3, name: "nip", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Nomor Induk Pegawai" },
      { no: 4, name: "email", type: "VARCHAR(255)", constraint: "UNIQUE", desc: "Email untuk login" },
      { no: 5, name: "password", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Password terenkripsi (bcrypt)" },
      { no: 6, name: "role", type: "ENUM", constraint: "NOT NULL", desc: "super_admin, wali_kelas, orang_tua, guru, siswa, kepala_sekolah" },
      { no: 7, name: "is_approved", type: "BOOLEAN", constraint: "DEFAULT false", desc: "Status persetujuan akun" },
      { no: 8, name: "is_demo", type: "BOOLEAN", constraint: "DEFAULT false", desc: "Penanda akun demo" },
      { no: 9, name: "photo", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Path foto profil" },
      { no: 10, name: "email_verified_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu verifikasi email" },
      { no: 11, name: "remember_token", type: "VARCHAR(100)", constraint: "NULLABLE", desc: "Token Remember Me" },
      { no: 12, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 13, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "classes", label: "Classes (Kelas)",
    desc: "Menyimpan data kelas atau rombongan belajar di sekolah.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "name", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Nama kelas (mis: X-IPA-1)" },
      { no: 3, name: "grade", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Tingkat kelas (X, XI, XII)" },
      { no: 4, name: "major", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Jurusan (IPA, IPS, dll)" },
      { no: 5, name: "description", type: "TEXT", constraint: "NULLABLE", desc: "Deskripsi kelas" },
      { no: 6, name: "status", type: "ENUM", constraint: "DEFAULT active", desc: "active / inactive" },
      { no: 7, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 8, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "students", label: "Students (Siswa)",
    desc: "Menyimpan data lengkap siswa. Setiap siswa memiliki barcode unik (UUID) yang di-generate otomatis untuk QR Code.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "nisn", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Nomor Induk Siswa Nasional" },
      { no: 3, name: "nis", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Nomor Induk Siswa (lokal)" },
      { no: 4, name: "name", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Nama lengkap siswa" },
      { no: 5, name: "email", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Email siswa" },
      { no: 6, name: "gender", type: "ENUM", constraint: "NOT NULL", desc: "L (Laki-laki) / P (Perempuan)" },
      { no: 7, name: "class_id", type: "BIGINT", constraint: "FK", desc: "Kelas siswa", ref: "classes(id)" },
      { no: 8, name: "phone_number", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Nomor telepon/HP" },
      { no: 9, name: "address", type: "TEXT", constraint: "NULLABLE", desc: "Alamat tempat tinggal" },
      { no: 10, name: "birth_place", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Tempat lahir" },
      { no: 11, name: "birth_date", type: "DATE", constraint: "NULLABLE", desc: "Tanggal lahir" },
      { no: 12, name: "photo", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Path foto siswa" },
      { no: 13, name: "status", type: "ENUM", constraint: "DEFAULT active", desc: "active / inactive" },
      { no: 14, name: "barcode_data", type: "VARCHAR(255)", constraint: "UNIQUE", desc: "UUID unik untuk QR Code" },
      { no: 15, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 16, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "homeroom_teachers", label: "Homeroom Teachers (Wali Kelas)",
    desc: "Tabel penugasan wali kelas. Menghubungkan satu user (guru) dengan satu kelas yang diampu.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "user_id", type: "BIGINT", constraint: "FK", desc: "Guru wali kelas", ref: "users(id)" },
      { no: 3, name: "class_id", type: "BIGINT", constraint: "FK", desc: "Kelas yang diampu (nullable)", ref: "classes(id)" },
      { no: 4, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 5, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "parents", label: "Parents (Orang Tua)",
    desc: "Data orang tua/wali murid. Setiap orang tua terhubung ke akun user dan dapat memiliki banyak anak (siswa) melalui tabel pivot.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "user_id", type: "BIGINT", constraint: "FK", desc: "Akun login orang tua", ref: "users(id)" },
      { no: 3, name: "name", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Nama orang tua" },
      { no: 4, name: "phone_number", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Nomor WhatsApp/HP" },
      { no: 5, name: "relation_status", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Ayah / Ibu / Wali" },
      { no: 6, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 7, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "parent_student", label: "Parent Student (Tabel Pivot)",
    desc: "Tabel pivot untuk relasi Many-to-Many antara orang tua dan siswa.",
    cols: [
      { no: 1, name: "parent_id", type: "BIGINT", constraint: "FK", desc: "ID orang tua", ref: "parents(id)" },
      { no: 2, name: "student_id", type: "BIGINT", constraint: "FK", desc: "ID siswa", ref: "students(id)" },
    ],
  },
  {
    name: "absences", label: "Absences (Absensi Harian Siswa)",
    desc: "Menyimpan data absensi harian siswa termasuk waktu masuk, pulang, status kehadiran, durasi terlambat, dan lokasi GPS.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "student_id", type: "BIGINT", constraint: "FK", desc: "Siswa yang diabsen", ref: "students(id)" },
      { no: 3, name: "attendance_time", type: "DATETIME", constraint: "NOT NULL", desc: "Waktu check-in (masuk)" },
      { no: 4, name: "checkout_time", type: "DATETIME", constraint: "NULLABLE", desc: "Waktu check-out (pulang)" },
      { no: 5, name: "status", type: "ENUM", constraint: "NOT NULL", desc: "hadir, terlambat, alpha, sakit, izin" },
      { no: 6, name: "late_duration", type: "INTEGER", constraint: "DEFAULT 0", desc: "Durasi terlambat (menit)" },
      { no: 7, name: "reason", type: "TEXT", constraint: "NULLABLE", desc: "Alasan ketidakhadiran" },
      { no: 8, name: "recorded_by", type: "BIGINT", constraint: "FK", desc: "User pencatat absensi", ref: "users(id)" },
      { no: 9, name: "latitude", type: "DECIMAL(10,8)", constraint: "NULLABLE", desc: "Koordinat GPS lintang" },
      { no: 10, name: "longitude", type: "DECIMAL(11,8)", constraint: "NULLABLE", desc: "Koordinat GPS bujur" },
      { no: 11, name: "ip_address", type: "VARCHAR(45)", constraint: "NULLABLE", desc: "Alamat IP perangkat" },
      { no: 12, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 13, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "izin_requests", label: "Izin Requests (Pengajuan Izin/Sakit)",
    desc: "Menyimpan data pengajuan izin atau sakit dari siswa. Dapat disetujui atau ditolak oleh wali kelas/admin.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "student_id", type: "BIGINT", constraint: "FK", desc: "Siswa yang mengajukan", ref: "students(id)" },
      { no: 3, name: "request_date", type: "DATE", constraint: "NOT NULL", desc: "Tanggal izin" },
      { no: 4, name: "type", type: "ENUM", constraint: "NOT NULL", desc: "sakit / izin" },
      { no: 5, name: "reason", type: "TEXT", constraint: "NOT NULL", desc: "Alasan pengajuan" },
      { no: 6, name: "attachment_path", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Path file lampiran" },
      { no: 7, name: "status", type: "ENUM", constraint: "DEFAULT pending", desc: "pending, approved, rejected" },
      { no: 8, name: "approved_by", type: "BIGINT", constraint: "FK", desc: "User yang menyetujui", ref: "users(id)" },
      { no: 9, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 10, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "settings", label: "Settings (Pengaturan Sekolah)",
    desc: "Menyimpan konfigurasi aplikasi dalam format key-value (nama sekolah, WhatsApp API, lokasi GPS, dll).",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "key", type: "VARCHAR(255)", constraint: "UNIQUE", desc: "Kunci setting" },
      { no: 3, name: "value", type: "TEXT", constraint: "NULLABLE", desc: "Nilai setting" },
      { no: 4, name: "description", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Deskripsi setting" },
      { no: 5, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 6, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "announcements", label: "Announcements (Pengumuman)",
    desc: "Menyimpan data pengumuman sekolah yang dapat ditargetkan ke semua pengguna atau kelas tertentu.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "title", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Judul pengumuman" },
      { no: 3, name: "content", type: "TEXT", constraint: "NOT NULL", desc: "Isi pengumuman" },
      { no: 4, name: "target_type", type: "ENUM", constraint: "NOT NULL", desc: "all / class" },
      { no: 5, name: "target_id", type: "BIGINT", constraint: "FK", desc: "Kelas target (jika class)", ref: "classes(id)" },
      { no: 6, name: "is_active", type: "BOOLEAN", constraint: "DEFAULT true", desc: "Status aktif" },
      { no: 7, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 8, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "subjects", label: "Subjects (Mata Pelajaran)",
    desc: "Menyimpan daftar mata pelajaran yang diajarkan di sekolah.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "name", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Nama mata pelajaran" },
      { no: 3, name: "code", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Kode mapel (MTK, IPA)" },
      { no: 4, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 5, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "schedules", label: "Schedules (Jadwal Pelajaran)",
    desc: "Menyimpan jadwal pelajaran yang menghubungkan kelas, mata pelajaran, dan guru pengajar.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "class_id", type: "BIGINT", constraint: "FK", desc: "Kelas", ref: "classes(id)" },
      { no: 3, name: "subject_id", type: "BIGINT", constraint: "FK", desc: "Mata pelajaran", ref: "subjects(id)" },
      { no: 4, name: "teacher_id", type: "BIGINT", constraint: "FK", desc: "Guru pengajar", ref: "users(id)" },
      { no: 5, name: "day", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Hari (Senin-Jumat)" },
      { no: 6, name: "start_time", type: "TIME", constraint: "NOT NULL", desc: "Jam mulai" },
      { no: 7, name: "end_time", type: "TIME", constraint: "NOT NULL", desc: "Jam selesai" },
      { no: 8, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 9, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "teaching_journals", label: "Teaching Journals (Jurnal Mengajar)",
    desc: "Jurnal mengajar guru. Mencatat materi yang diajarkan pada setiap sesi pelajaran.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "schedule_id", type: "BIGINT", constraint: "FK", desc: "Jadwal pelajaran", ref: "schedules(id)" },
      { no: 3, name: "teacher_id", type: "BIGINT", constraint: "FK", desc: "Guru pengajar", ref: "users(id)" },
      { no: 4, name: "date", type: "DATE", constraint: "NOT NULL", desc: "Tanggal mengajar" },
      { no: 5, name: "start_time", type: "TIME", constraint: "NULLABLE", desc: "Jam mulai aktual" },
      { no: 6, name: "end_time", type: "TIME", constraint: "NULLABLE", desc: "Jam selesai aktual" },
      { no: 7, name: "topic", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Topik/materi" },
      { no: 8, name: "notes", type: "TEXT", constraint: "NULLABLE", desc: "Catatan guru" },
      { no: 9, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 10, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "subject_attendances", label: "Subject Attendances (Absensi Per Mapel)",
    desc: "Absensi siswa per sesi mata pelajaran, terkait dengan jurnal mengajar guru.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "teaching_journal_id", type: "BIGINT", constraint: "FK", desc: "Jurnal mengajar", ref: "teaching_journals(id)" },
      { no: 3, name: "student_id", type: "BIGINT", constraint: "FK", desc: "Siswa", ref: "students(id)" },
      { no: 4, name: "status", type: "VARCHAR(255)", constraint: "NOT NULL", desc: "Status kehadiran" },
      { no: 5, name: "notes", type: "TEXT", constraint: "NULLABLE", desc: "Catatan" },
      { no: 6, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 7, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
  {
    name: "teacher_attendances", label: "Teacher Attendances (Absensi Guru)",
    desc: "Menyimpan data absensi guru dan staff, termasuk foto selfie dan koordinat GPS sebagai bukti kehadiran.",
    cols: [
      { no: 1, name: "id", type: "BIGINT", constraint: "PK", desc: "Primary Key, auto increment" },
      { no: 2, name: "user_id", type: "BIGINT", constraint: "FK", desc: "User guru/staff", ref: "users(id)" },
      { no: 3, name: "date", type: "DATE", constraint: "NOT NULL", desc: "Tanggal absensi" },
      { no: 4, name: "clock_in", type: "TIME", constraint: "NULLABLE", desc: "Jam masuk" },
      { no: 5, name: "clock_out", type: "TIME", constraint: "NULLABLE", desc: "Jam pulang" },
      { no: 6, name: "status", type: "ENUM", constraint: "NOT NULL", desc: "present, late, permission, sick, alpha" },
      { no: 7, name: "latitude", type: "DECIMAL(10,8)", constraint: "NULLABLE", desc: "Koordinat GPS lintang" },
      { no: 8, name: "longitude", type: "DECIMAL(11,8)", constraint: "NULLABLE", desc: "Koordinat GPS bujur" },
      { no: 9, name: "photo", type: "VARCHAR(255)", constraint: "NULLABLE", desc: "Path foto selfie" },
      { no: 10, name: "created_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu pembuatan" },
      { no: 11, name: "updated_at", type: "TIMESTAMP", constraint: "NULLABLE", desc: "Waktu update terakhir" },
    ],
  },
];

const relations = [
  { no: 1, from: "users", card: "1 : 1", to: "homeroom_teachers", fk: "user_id", desc: "Wali kelas" },
  { no: 2, from: "users", card: "1 : 1", to: "parents", fk: "user_id", desc: "Akun orang tua" },
  { no: 3, from: "users", card: "1 : N", to: "schedules", fk: "teacher_id", desc: "Guru mengajar" },
  { no: 4, from: "users", card: "1 : N", to: "teacher_attendances", fk: "user_id", desc: "Absensi guru" },
  { no: 5, from: "users", card: "1 : N", to: "teaching_journals", fk: "teacher_id", desc: "Jurnal mengajar" },
  { no: 6, from: "users", card: "1 : N", to: "izin_requests", fk: "approved_by", desc: "Approver izin" },
  { no: 7, from: "users", card: "1 : N", to: "absences", fk: "recorded_by", desc: "Pencatat absensi" },
  { no: 8, from: "classes", card: "1 : N", to: "students", fk: "class_id", desc: "Siswa dalam kelas" },
  { no: 9, from: "classes", card: "1 : 1", to: "homeroom_teachers", fk: "class_id", desc: "Kelas diampu" },
  { no: 10, from: "classes", card: "1 : N", to: "schedules", fk: "class_id", desc: "Jadwal kelas" },
  { no: 11, from: "classes", card: "1 : N", to: "announcements", fk: "target_id", desc: "Target pengumuman" },
  { no: 12, from: "students", card: "1 : N", to: "absences", fk: "student_id", desc: "Absensi harian" },
  { no: 13, from: "students", card: "1 : N", to: "izin_requests", fk: "student_id", desc: "Pengajuan izin" },
  { no: 14, from: "students", card: "1 : N", to: "subject_attendances", fk: "student_id", desc: "Absensi mapel" },
  { no: 15, from: "students", card: "M : N", to: "parents", fk: "parent_student", desc: "Via pivot table" },
  { no: 16, from: "subjects", card: "1 : N", to: "schedules", fk: "subject_id", desc: "Jadwal mapel" },
  { no: 17, from: "schedules", card: "1 : N", to: "teaching_journals", fk: "schedule_id", desc: "Jurnal per jadwal" },
  { no: 18, from: "teaching_journals", card: "1 : N", to: "subject_attendances", fk: "teaching_journal_id", desc: "Absensi per sesi" },
];

const fkMapping = [
  { no: 1, table: "students", fk: "class_id", refTable: "classes", refCol: "id", card: "N : 1" },
  { no: 2, table: "homeroom_teachers", fk: "user_id", refTable: "users", refCol: "id", card: "1 : 1" },
  { no: 3, table: "homeroom_teachers", fk: "class_id", refTable: "classes", refCol: "id", card: "1 : 1" },
  { no: 4, table: "parents", fk: "user_id", refTable: "users", refCol: "id", card: "1 : 1" },
  { no: 5, table: "parent_student", fk: "parent_id", refTable: "parents", refCol: "id", card: "M : N" },
  { no: 6, table: "parent_student", fk: "student_id", refTable: "students", refCol: "id", card: "M : N" },
  { no: 7, table: "absences", fk: "student_id", refTable: "students", refCol: "id", card: "N : 1" },
  { no: 8, table: "absences", fk: "recorded_by", refTable: "users", refCol: "id", card: "N : 1" },
  { no: 9, table: "izin_requests", fk: "student_id", refTable: "students", refCol: "id", card: "N : 1" },
  { no: 10, table: "izin_requests", fk: "approved_by", refTable: "users", refCol: "id", card: "N : 1" },
  { no: 11, table: "announcements", fk: "target_id", refTable: "classes", refCol: "id", card: "N : 1" },
  { no: 12, table: "schedules", fk: "class_id", refTable: "classes", refCol: "id", card: "N : 1" },
  { no: 13, table: "schedules", fk: "subject_id", refTable: "subjects", refCol: "id", card: "N : 1" },
  { no: 14, table: "schedules", fk: "teacher_id", refTable: "users", refCol: "id", card: "N : 1" },
  { no: 15, table: "teaching_journals", fk: "schedule_id", refTable: "schedules", refCol: "id", card: "N : 1" },
  { no: 16, table: "teaching_journals", fk: "teacher_id", refTable: "users", refCol: "id", card: "N : 1" },
  { no: 17, table: "subject_attendances", fk: "teaching_journal_id", refTable: "teaching_journals", refCol: "id", card: "N : 1" },
  { no: 18, table: "subject_attendances", fk: "student_id", refTable: "students", refCol: "id", card: "N : 1" },
  { no: 19, table: "teacher_attendances", fk: "user_id", refTable: "users", refCol: "id", card: "N : 1" },
];

// ======================== BUILD ERD DOCUMENT ========================
function buildERD() {
  const sections = [];

  // Cover
  sections.push(
    emptyLine(), emptyLine(), emptyLine(), emptyLine(), emptyLine(),
    emptyLine(), emptyLine(), emptyLine(),
    new Paragraph({ children: [new TextRun({ text: "ENTITY RELATIONSHIP DIAGRAM", bold: true, size: 48, font: "Calibri", color: C.primary })], alignment: AlignmentType.CENTER }),
    new Paragraph({ children: [new TextRun({ text: "(ERD)", size: 32, font: "Calibri", color: C.gray })], alignment: AlignmentType.CENTER, spacing: { before: 100 } }),
    emptyLine(),
    new Paragraph({ children: [new TextRun({ text: "Aplikasi E-Absensi Siswa", bold: true, size: 28, font: "Calibri", color: C.success })], alignment: AlignmentType.CENTER }),
    new Paragraph({ children: [new TextRun({ text: "Sistem Informasi Manajemen Presensi Siswa Berbasis Web", size: 24, font: "Calibri", color: C.gray })], alignment: AlignmentType.CENTER, spacing: { before: 100 } }),
    emptyLine(), emptyLine(), emptyLine(), emptyLine(), emptyLine(),
    emptyLine(), emptyLine(), emptyLine(),
    new Paragraph({ children: [new TextRun({ text: "Dokumen ini dibuat secara otomatis — April 2026", size: 20, font: "Calibri", color: C.lightGray })], alignment: AlignmentType.CENTER }),
    pageBreak(),
  );

  // Bab 1
  sections.push(
    heading("1. Pendahuluan"),
    para("Dokumen ini menyajikan Entity Relationship Diagram (ERD) dari aplikasi E-Absensi Siswa, yaitu sistem informasi manajemen presensi siswa berbasis web yang dirancang untuk mempermudah proses pencatatan kehadiran siswa dan guru di lingkungan sekolah."),
    para("ERD menggambarkan entitas-entitas utama dalam sistem beserta atribut dan relasi antar entitas tersebut. Diagram ini menjadi acuan dalam perancangan basis data (database schema) yang digunakan oleh aplikasi."),
    emptyLine(),
    para("Teknologi Database: MySQL / MariaDB  |  Framework: Laravel 12  |  Jumlah Entitas: 15 tabel  |  Jumlah Relasi: 18 relasi", { bold: true }),
    emptyLine(),
  );

  // Bab 2 - Daftar Entitas
  sections.push(
    heading("2. Daftar Entitas"),
    para("Berikut adalah daftar seluruh entitas (tabel) dalam sistem E-Absensi Siswa:"),
    tableFromData(
      ["No", "Nama Tabel", "Domain", "Keterangan"],
      [8, 25, 20, 47],
      tables.map((t, i) => [
        { _custom: true, text: String(i + 1), align: AlignmentType.CENTER },
        { _custom: true, text: t.name, bold: true },
        t.label.split("(")[1]?.replace(")", "").trim() || "",
        t.desc.substring(0, 60) + "...",
      ])
    ),
    pageBreak(),
  );

  // Bab 3 - Detail Entitas
  sections.push(heading("3. Detail Entitas dan Atribut"));

  tables.forEach((t, idx) => {
    sections.push(
      heading(`3.${idx + 1}. Tabel ${t.name}`, HeadingLevel.HEADING_3),
      para(t.desc),
      tableFromData(
        ["No", "Nama Kolom", "Tipe Data", "Constraint", "Keterangan"],
        [6, 22, 16, 16, 40],
        t.cols.map((col) => {
          const badge = col.constraint === "PK" ? "PK" : col.constraint === "FK" ? "FK" : col.constraint === "UNIQUE" ? "UK" : null;
          return [
            { _custom: true, text: String(col.no), align: AlignmentType.CENTER },
            { _custom: true, text: col.name, bold: true },
            col.type,
            badge ? { _custom: true, text: col.constraint, badge } : col.constraint,
            col.ref ? { _custom: true, text: col.desc, ref: col.ref } : col.desc,
          ];
        })
      ),
      emptyLine(),
    );
    if ((idx + 1) % 3 === 0 && idx < tables.length - 1) sections.push(pageBreak());
  });

  // Bab 4 - Relasi
  sections.push(
    pageBreak(),
    heading("4. Relasi Antar Entitas"),
    para("Berikut adalah daftar seluruh relasi antar entitas dalam sistem E-Absensi Siswa:"),
    tableFromData(
      ["No", "Entitas Asal", "Kardinalitas", "Entitas Tujuan", "Foreign Key", "Keterangan"],
      [6, 18, 14, 18, 18, 26],
      relations.map((r) => [
        { _custom: true, text: String(r.no), align: AlignmentType.CENTER },
        { _custom: true, text: r.from, bold: true },
        { _custom: true, text: r.card, align: AlignmentType.CENTER, bold: true, color: C.danger },
        { _custom: true, text: r.to, bold: true },
        r.fk,
        r.desc,
      ])
    ),
  );

  return new Document({
    sections: [{ children: sections }],
    styles: {
      default: {
        document: { run: { font: "Calibri", size: 22 } },
      },
    },
  });
}

// ======================== BUILD LRS DOCUMENT ========================
function buildLRS() {
  const sections = [];

  // Cover
  sections.push(
    emptyLine(), emptyLine(), emptyLine(), emptyLine(), emptyLine(),
    emptyLine(), emptyLine(), emptyLine(),
    new Paragraph({ children: [new TextRun({ text: "LOGICAL RECORD STRUCTURE", bold: true, size: 48, font: "Calibri", color: C.primary })], alignment: AlignmentType.CENTER }),
    new Paragraph({ children: [new TextRun({ text: "(LRS)", size: 32, font: "Calibri", color: C.gray })], alignment: AlignmentType.CENTER, spacing: { before: 100 } }),
    emptyLine(),
    new Paragraph({ children: [new TextRun({ text: "Aplikasi E-Absensi Siswa", bold: true, size: 28, font: "Calibri", color: C.success })], alignment: AlignmentType.CENTER }),
    new Paragraph({ children: [new TextRun({ text: "Sistem Informasi Manajemen Presensi Siswa Berbasis Web", size: 24, font: "Calibri", color: C.gray })], alignment: AlignmentType.CENTER, spacing: { before: 100 } }),
    emptyLine(), emptyLine(), emptyLine(), emptyLine(), emptyLine(),
    emptyLine(), emptyLine(), emptyLine(),
    new Paragraph({ children: [new TextRun({ text: "Dokumen ini dibuat secara otomatis — April 2026", size: 20, font: "Calibri", color: C.lightGray })], alignment: AlignmentType.CENTER }),
    pageBreak(),
  );

  // Bab 1
  sections.push(
    heading("1. Pendahuluan"),
    para("Dokumen ini menyajikan Logical Record Structure (LRS) dari aplikasi E-Absensi Siswa. LRS merupakan representasi dari struktur record secara logis yang menggambarkan setiap tabel beserta field-field di dalamnya serta hubungan antar tabel melalui foreign key."),
    para("Berbeda dengan ERD yang menggambarkan hubungan konseptual antar entitas, LRS menggambarkan struktur fisik tabel yang akan diimplementasikan dalam basis data. Setiap record (baris) dalam tabel ditunjukkan dengan field-field lengkap beserta tipe datanya."),
    emptyLine(),
    para("Teknologi Database: MySQL / MariaDB  |  Framework: Laravel 12 (Eloquent ORM)  |  Jumlah Tabel: 15  |  Jumlah FK: 19", { bold: true }),
    emptyLine(),
  );

  // Bab 2 - Perbedaan ERD vs LRS
  sections.push(
    heading("2. Perbedaan ERD dan LRS"),
    tableFromData(
      ["Aspek", "ERD", "LRS"],
      [18, 41, 41],
      [
        [{ _custom: true, text: "Fokus", bold: true }, "Hubungan antar entitas secara konseptual", "Struktur field dan koneksi FK antar tabel"],
        [{ _custom: true, text: "Level", bold: true }, "Konseptual / Logical", "Logical / Physical"],
        [{ _custom: true, text: "Representasi", bold: true }, "Entitas, Atribut, Relasi", "Tabel dengan field lengkap dan tipe data"],
        [{ _custom: true, text: "Foreign Key", bold: true }, "Implisit melalui garis relasi", "Eksplisit dengan referensi tabel.kolom"],
        [{ _custom: true, text: "Tipe Data", bold: true }, "Tidak selalu ada", "Selalu ditampilkan"],
        [{ _custom: true, text: "Kegunaan", bold: true }, "Tahap perancangan awal", "Tahap implementasi database"],
      ]
    ),
    pageBreak(),
  );

  // Bab 3 - Struktur Logis
  sections.push(heading("3. Struktur Logis Tabel (LRS)"));
  sections.push(para("Berikut adalah struktur logis setiap tabel. Simbol * menandakan Primary Key, ** menandakan Foreign Key."));

  tables.forEach((t, idx) => {
    sections.push(
      heading(`3.${idx + 1}. ${t.name}`, HeadingLevel.HEADING_3),
      para(t.desc),
    );

    // LRS-style table: Field | Type | Constraint | Reference
    const lrsRows = t.cols.map((col) => {
      const prefix = col.constraint === "PK" ? "*" : col.constraint === "FK" ? "**" : "";
      const badge = col.constraint === "PK" ? "PK" : col.constraint === "FK" ? "FK" : col.constraint === "UNIQUE" ? "UK" : null;
      return [
        { _custom: true, text: `${prefix}${col.name}`, bold: true, color: col.constraint === "PK" ? C.accent : col.constraint === "FK" ? C.primaryLight : C.dark },
        col.type,
        badge ? { _custom: true, text: col.constraint, badge } : col.constraint,
        col.ref ? { _custom: true, text: `→ ${col.ref}`, color: C.danger, italic: true } : "—",
      ];
    });

    sections.push(
      tableFromData(
        ["Field", "Tipe Data", "Constraint", "Referensi FK"],
        [25, 22, 20, 33],
        lrsRows
      ),
      emptyLine(),
    );
    if ((idx + 1) % 3 === 0 && idx < tables.length - 1) sections.push(pageBreak());
  });

  // Bab 4 - Pemetaan FK
  sections.push(
    pageBreak(),
    heading("4. Pemetaan Foreign Key (FK)"),
    para("Berikut adalah pemetaan seluruh foreign key yang menghubungkan antar tabel dalam LRS:"),
    tableFromData(
      ["No", "Tabel Asal", "Kolom FK", "Tabel Referensi", "Kolom Ref", "Kardinalitas"],
      [6, 20, 20, 20, 12, 12],
      fkMapping.map((fk) => [
        { _custom: true, text: String(fk.no), align: AlignmentType.CENTER },
        { _custom: true, text: fk.table, bold: true },
        { _custom: true, text: fk.fk, bold: true, color: C.primaryLight },
        { _custom: true, text: fk.refTable, bold: true },
        fk.refCol,
        { _custom: true, text: fk.card, align: AlignmentType.CENTER, bold: true, color: C.danger },
      ])
    ),
  );

  return new Document({
    sections: [{ children: sections }],
    styles: {
      default: {
        document: { run: { font: "Calibri", size: 22 } },
      },
    },
  });
}

// ======================== GENERATE FILES ========================
async function main() {
  console.log("📘 Generating ERD_E-Absensi-Siswa.docx ...");
  const erdDoc = buildERD();
  const erdBuffer = await Packer.toBuffer(erdDoc);
  fs.writeFileSync(path.join(__dirname, "ERD_E-Absensi-Siswa.docx"), erdBuffer);
  console.log("   ✅ ERD document created!");

  console.log("📗 Generating LRS_E-Absensi-Siswa.docx ...");
  const lrsDoc = buildLRS();
  const lrsBuffer = await Packer.toBuffer(lrsDoc);
  fs.writeFileSync(path.join(__dirname, "LRS_E-Absensi-Siswa.docx"), lrsBuffer);
  console.log("   ✅ LRS document created!");

  console.log("\n🎉 Done! Files saved to docs/ folder.");
}

main().catch(console.error);
