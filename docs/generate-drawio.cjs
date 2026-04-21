const fs = require("fs");
const path = require("path");

// ======================== TABLE DATA ========================
const tables = [
  {
    id: "users", label: "users", color: "#1A5276",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "name", type: "VARCHAR(255)" },
      { name: "nip", type: "VARCHAR(255)" },
      { name: "email", type: "VARCHAR(255)", key: "UK" },
      { name: "password", type: "VARCHAR(255)" },
      { name: "role", type: "ENUM" },
      { name: "is_approved", type: "BOOLEAN" },
      { name: "is_demo", type: "BOOLEAN" },
      { name: "photo", type: "VARCHAR(255)" },
      { name: "email_verified_at", type: "TIMESTAMP" },
      { name: "remember_token", type: "VARCHAR(100)" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "classes", label: "classes", color: "#2E86C1",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "name", type: "VARCHAR(255)" },
      { name: "grade", type: "VARCHAR(255)" },
      { name: "major", type: "VARCHAR(255)" },
      { name: "description", type: "TEXT" },
      { name: "status", type: "ENUM" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "students", label: "students", color: "#148F77",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "nisn", type: "VARCHAR(255)" },
      { name: "nis", type: "VARCHAR(255)" },
      { name: "name", type: "VARCHAR(255)" },
      { name: "email", type: "VARCHAR(255)" },
      { name: "gender", type: "ENUM" },
      { name: "class_id", type: "BIGINT", key: "FK", ref: "classes.id" },
      { name: "phone_number", type: "VARCHAR(255)" },
      { name: "address", type: "TEXT" },
      { name: "birth_place", type: "VARCHAR(255)" },
      { name: "birth_date", type: "DATE" },
      { name: "photo", type: "VARCHAR(255)" },
      { name: "status", type: "ENUM" },
      { name: "barcode_data", type: "VARCHAR(255)", key: "UK" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "homeroom_teachers", label: "homeroom_teachers", color: "#7D3C98",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "user_id", type: "BIGINT", key: "FK", ref: "users.id" },
      { name: "class_id", type: "BIGINT", key: "FK", ref: "classes.id" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "parents", label: "parents", color: "#D35400",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "user_id", type: "BIGINT", key: "FK", ref: "users.id" },
      { name: "name", type: "VARCHAR(255)" },
      { name: "phone_number", type: "VARCHAR(255)" },
      { name: "relation_status", type: "VARCHAR(255)" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "parent_student", label: "parent_student", color: "#CA6F1E",
    fields: [
      { name: "parent_id", type: "BIGINT", key: "FK", ref: "parents.id" },
      { name: "student_id", type: "BIGINT", key: "FK", ref: "students.id" },
    ],
  },
  {
    id: "absences", label: "absences", color: "#C0392B",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "student_id", type: "BIGINT", key: "FK", ref: "students.id" },
      { name: "attendance_time", type: "DATETIME" },
      { name: "checkout_time", type: "DATETIME" },
      { name: "status", type: "ENUM" },
      { name: "late_duration", type: "INTEGER" },
      { name: "reason", type: "TEXT" },
      { name: "recorded_by", type: "BIGINT", key: "FK", ref: "users.id" },
      { name: "latitude", type: "DECIMAL(10,8)" },
      { name: "longitude", type: "DECIMAL(11,8)" },
      { name: "ip_address", type: "VARCHAR(45)" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "izin_requests", label: "izin_requests", color: "#E74C3C",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "student_id", type: "BIGINT", key: "FK", ref: "students.id" },
      { name: "request_date", type: "DATE" },
      { name: "type", type: "ENUM" },
      { name: "reason", type: "TEXT" },
      { name: "attachment_path", type: "VARCHAR(255)" },
      { name: "status", type: "ENUM" },
      { name: "approved_by", type: "BIGINT", key: "FK", ref: "users.id" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "settings", label: "settings", color: "#566573",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "key", type: "VARCHAR(255)", key: "UK" },
      { name: "value", type: "TEXT" },
      { name: "description", type: "VARCHAR(255)" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "announcements", label: "announcements", color: "#2874A6",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "title", type: "VARCHAR(255)" },
      { name: "content", type: "TEXT" },
      { name: "target_type", type: "ENUM" },
      { name: "target_id", type: "BIGINT", key: "FK", ref: "classes.id" },
      { name: "is_active", type: "BOOLEAN" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "subjects", label: "subjects", color: "#1ABC9C",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "name", type: "VARCHAR(255)" },
      { name: "code", type: "VARCHAR(255)" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "schedules", label: "schedules", color: "#2471A3",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "class_id", type: "BIGINT", key: "FK", ref: "classes.id" },
      { name: "subject_id", type: "BIGINT", key: "FK", ref: "subjects.id" },
      { name: "teacher_id", type: "BIGINT", key: "FK", ref: "users.id" },
      { name: "day", type: "VARCHAR(255)" },
      { name: "start_time", type: "TIME" },
      { name: "end_time", type: "TIME" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "teaching_journals", label: "teaching_journals", color: "#1F618D",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "schedule_id", type: "BIGINT", key: "FK", ref: "schedules.id" },
      { name: "teacher_id", type: "BIGINT", key: "FK", ref: "users.id" },
      { name: "date", type: "DATE" },
      { name: "start_time", type: "TIME" },
      { name: "end_time", type: "TIME" },
      { name: "topic", type: "VARCHAR(255)" },
      { name: "notes", type: "TEXT" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "subject_attendances", label: "subject_attendances", color: "#117A65",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "teaching_journal_id", type: "BIGINT", key: "FK", ref: "teaching_journals.id" },
      { name: "student_id", type: "BIGINT", key: "FK", ref: "students.id" },
      { name: "status", type: "VARCHAR(255)" },
      { name: "notes", type: "TEXT" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
  {
    id: "teacher_attendances", label: "teacher_attendances", color: "#922B21",
    fields: [
      { name: "id", type: "BIGINT", key: "PK" },
      { name: "user_id", type: "BIGINT", key: "FK", ref: "users.id" },
      { name: "date", type: "DATE" },
      { name: "clock_in", type: "TIME" },
      { name: "clock_out", type: "TIME" },
      { name: "status", type: "ENUM" },
      { name: "latitude", type: "DECIMAL(10,8)" },
      { name: "longitude", type: "DECIMAL(11,8)" },
      { name: "photo", type: "VARCHAR(255)" },
      { name: "created_at", type: "TIMESTAMP" },
      { name: "updated_at", type: "TIMESTAMP" },
    ],
  },
];

// Layout positions
const layout = {
  users:               { x: 40,   y: 20 },
  homeroom_teachers:   { x: 340,  y: 80 },
  classes:             { x: 640,  y: 20 },
  students:            { x: 640,  y: 320 },
  parents:             { x: 40,   y: 470 },
  parent_student:      { x: 340,  y: 530 },
  absences:            { x: 340,  y: 700 },
  izin_requests:       { x: 640,  y: 870 },
  settings:            { x: 1260, y: 20 },
  announcements:       { x: 960,  y: 20 },
  subjects:            { x: 1260, y: 260 },
  schedules:           { x: 960,  y: 320 },
  teaching_journals:   { x: 960,  y: 640 },
  subject_attendances: { x: 640,  y: 1190 },
  teacher_attendances: { x: 40,   y: 800 },
};

// FK relationships for drawing edges
const relationships = [
  { from: "homeroom_teachers", fromField: "user_id",    to: "users",             toField: "id", label: "1:1" },
  { from: "homeroom_teachers", fromField: "class_id",   to: "classes",           toField: "id", label: "1:1" },
  { from: "parents",           fromField: "user_id",    to: "users",             toField: "id", label: "1:1" },
  { from: "students",          fromField: "class_id",   to: "classes",           toField: "id", label: "1:N" },
  { from: "parent_student",    fromField: "parent_id",  to: "parents",           toField: "id", label: "M:N" },
  { from: "parent_student",    fromField: "student_id", to: "students",          toField: "id", label: "M:N" },
  { from: "absences",          fromField: "student_id", to: "students",          toField: "id", label: "1:N" },
  { from: "absences",          fromField: "recorded_by",to: "users",             toField: "id", label: "1:N" },
  { from: "izin_requests",     fromField: "student_id", to: "students",          toField: "id", label: "1:N" },
  { from: "izin_requests",     fromField: "approved_by",to: "users",             toField: "id", label: "1:N" },
  { from: "announcements",     fromField: "target_id",  to: "classes",           toField: "id", label: "N:1" },
  { from: "schedules",         fromField: "class_id",   to: "classes",           toField: "id", label: "N:1" },
  { from: "schedules",         fromField: "subject_id", to: "subjects",          toField: "id", label: "N:1" },
  { from: "schedules",         fromField: "teacher_id", to: "users",             toField: "id", label: "N:1" },
  { from: "teaching_journals", fromField: "schedule_id",to: "schedules",         toField: "id", label: "1:N" },
  { from: "teaching_journals", fromField: "teacher_id", to: "users",             toField: "id", label: "1:N" },
  { from: "subject_attendances",fromField:"teaching_journal_id",to:"teaching_journals",toField:"id",label:"1:N"},
  { from: "subject_attendances",fromField: "student_id",to: "students",          toField: "id", label: "1:N" },
  { from: "teacher_attendances",fromField: "user_id",   to: "users",             toField: "id", label: "1:N" },
];

const esc = (s) => s.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");

function lightenColor(hex) {
  const r = parseInt(hex.slice(1, 3), 16);
  const g = parseInt(hex.slice(3, 5), 16);
  const b = parseInt(hex.slice(5, 7), 16);
  const lr = Math.min(255, r + Math.round((255 - r) * 0.85));
  const lg = Math.min(255, g + Math.round((255 - g) * 0.85));
  const lb = Math.min(255, b + Math.round((255 - b) * 0.85));
  return `#${lr.toString(16).padStart(2,"0")}${lg.toString(16).padStart(2,"0")}${lb.toString(16).padStart(2,"0")}`;
}

// ======================== GENERATE ERD ========================
function generateERD() {
  const W = 220;
  const FIELD_H = 26;
  const HEADER_H = 30;
  let cellId = 2;
  const entityCellIds = {}; // tableId -> { container, fields: { fieldName: cellId } }
  let cells = "";

  // Build entities
  for (const t of tables) {
    const pos = layout[t.id];
    const H = HEADER_H + t.fields.length * FIELD_H;
    const containerId = cellId++;
    entityCellIds[t.id] = { container: containerId, fields: {} };

    const lightBg = lightenColor(t.color);

    cells += `    <mxCell id="${containerId}" value="${esc(t.label)}" style="swimlane;fontStyle=1;childLayout=stackLayout;horizontal=1;startSize=${HEADER_H};horizontalStack=0;resizeParent=1;resizeParentMax=0;collapsible=0;marginBottom=0;fillColor=${t.color};fontColor=#FFFFFF;swimlaneLine=0;rounded=1;arcSize=4;shadow=1;fontSize=13;" vertex="1" parent="1">\n`;
    cells += `      <mxGeometry x="${pos.x}" y="${pos.y}" width="${W}" height="${H}" as="geometry"/>\n`;
    cells += `    </mxCell>\n`;

    let yOff = HEADER_H;
    for (const f of t.fields) {
      const fId = cellId++;
      entityCellIds[t.id].fields[f.name] = fId;

      let label = f.name;
      let fontStyle = 0;
      let fontColor = "#2C3E50";
      if (f.key === "PK") { label = `🔑 ${f.name}`; fontStyle = 1; fontColor="#E67E22"; }
      else if (f.key === "FK") { label = `🔗 ${f.name}`; fontStyle = 0; fontColor="#2980B9"; }
      else if (f.key === "UK") { label = `✦ ${f.name}`; fontStyle = 0; fontColor="#27AE60"; }

      cells += `    <mxCell id="${fId}" value="${esc(label)}" style="text;strokeColor=none;fillColor=${lightBg};align=left;verticalAlign=middle;spacingLeft=8;spacingRight=4;overflow=hidden;rotatable=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontColor=${fontColor};fontStyle=${fontStyle};fontSize=11;" vertex="1" parent="${containerId}">\n`;
      cells += `      <mxGeometry y="${yOff}" width="${W}" height="${FIELD_H}" as="geometry"/>\n`;
      cells += `    </mxCell>\n`;
      yOff += FIELD_H;
    }
  }

  // Build edges
  for (const rel of relationships) {
    const edgeId = cellId++;
    const srcCell = entityCellIds[rel.from]?.fields[rel.fromField];
    const tgtCell = entityCellIds[rel.to]?.fields[rel.toField];
    if (!srcCell || !tgtCell) continue;

    let startArrow = "ERone";
    let endArrow = "ERone";
    if (rel.label.includes("N") && rel.label.indexOf("N") === 0) { startArrow = "ERmany"; }
    if (rel.label.includes("N") && rel.label.lastIndexOf("N") > 0) { endArrow = "ERmany"; }
    if (rel.label === "1:N") { startArrow = "ERone"; endArrow = "ERmany"; }
    if (rel.label === "N:1") { startArrow = "ERmany"; endArrow = "ERone"; }
    if (rel.label === "1:1") { startArrow = "ERmandOne"; endArrow = "ERmandOne"; }
    if (rel.label === "M:N") { startArrow = "ERmany"; endArrow = "ERmany"; }

    cells += `    <mxCell id="${edgeId}" value="${esc(rel.label)}" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;strokeColor=#6c8ebf;fontColor=#333333;fontSize=10;fontStyle=1;startArrow=${startArrow};endArrow=${endArrow};startFill=0;endFill=0;strokeWidth=1.5;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;" edge="1" parent="1" source="${srcCell}" target="${tgtCell}">\n`;
    cells += `      <mxGeometry relative="1" as="geometry"/>\n`;
    cells += `    </mxCell>\n`;
  }

  return `<?xml version="1.0" encoding="UTF-8"?>
<mxfile host="app.diagrams.net" type="device">
  <diagram name="ERD - E-Absensi Siswa" id="erd-e-absensi">
    <mxGraphModel dx="1422" dy="762" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="0" pageScale="1" pageWidth="1600" pageHeight="1800" math="0" shadow="0">
      <root>
        <mxCell id="0"/>
        <mxCell id="1" parent="0"/>
${cells}
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>`;
}

// ======================== GENERATE LRS ========================
function generateLRS() {
  const W = 310;
  const FIELD_H = 26;
  const HEADER_H = 30;
  let cellId = 2;
  const entityCellIds = {};
  let cells = "";

  // Wider spacing for LRS
  const lrsLayout = {
    users:               { x: 40,   y: 20 },
    homeroom_teachers:   { x: 420,  y: 80 },
    classes:             { x: 800,  y: 20 },
    students:            { x: 800,  y: 320 },
    parents:             { x: 40,   y: 470 },
    parent_student:      { x: 420,  y: 530 },
    absences:            { x: 420,  y: 700 },
    izin_requests:       { x: 800,  y: 870 },
    settings:            { x: 1540, y: 20 },
    announcements:       { x: 1180, y: 20 },
    subjects:            { x: 1540, y: 260 },
    schedules:           { x: 1180, y: 320 },
    teaching_journals:   { x: 1180, y: 640 },
    subject_attendances: { x: 800,  y: 1280 },
    teacher_attendances: { x: 40,   y: 850 },
  };

  for (const t of tables) {
    const pos = lrsLayout[t.id];
    const H = HEADER_H + t.fields.length * FIELD_H;
    const containerId = cellId++;
    entityCellIds[t.id] = { container: containerId, fields: {} };

    const lightBg = lightenColor(t.color);

    cells += `    <mxCell id="${containerId}" value="${esc(t.label)}" style="swimlane;fontStyle=1;childLayout=stackLayout;horizontal=1;startSize=${HEADER_H};horizontalStack=0;resizeParent=1;resizeParentMax=0;collapsible=0;marginBottom=0;fillColor=${t.color};fontColor=#FFFFFF;swimlaneLine=0;rounded=1;arcSize=4;shadow=1;fontSize=13;" vertex="1" parent="1">\n`;
    cells += `      <mxGeometry x="${pos.x}" y="${pos.y}" width="${W}" height="${H}" as="geometry"/>\n`;
    cells += `    </mxCell>\n`;

    let yOff = HEADER_H;
    for (const f of t.fields) {
      const fId = cellId++;
      entityCellIds[t.id].fields[f.name] = fId;

      let prefix = "";
      let fontColor = "#2C3E50";
      let fontStyle = 0;
      if (f.key === "PK") { prefix = "*"; fontColor = "#E67E22"; fontStyle = 1; }
      else if (f.key === "FK") { prefix = "**"; fontColor = "#2980B9"; fontStyle = 0; }
      else if (f.key === "UK") { prefix = ""; fontColor = "#27AE60"; fontStyle = 0; }

      let label = `${prefix}${f.name} : ${f.type}`;
      if (f.key) label += ` [${f.key}]`;
      if (f.ref) label += `  → ${f.ref}`;

      cells += `    <mxCell id="${fId}" value="${esc(label)}" style="text;strokeColor=none;fillColor=${lightBg};align=left;verticalAlign=middle;spacingLeft=8;spacingRight=4;overflow=hidden;rotatable=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;fontColor=${fontColor};fontStyle=${fontStyle};fontSize=10;fontFamily=Consolas;" vertex="1" parent="${containerId}">\n`;
      cells += `      <mxGeometry y="${yOff}" width="${W}" height="${FIELD_H}" as="geometry"/>\n`;
      cells += `    </mxCell>\n`;
      yOff += FIELD_H;
    }
  }

  // Build edges
  for (const rel of relationships) {
    const edgeId = cellId++;
    const srcCell = entityCellIds[rel.from]?.fields[rel.fromField];
    const tgtCell = entityCellIds[rel.to]?.fields[rel.toField];
    if (!srcCell || !tgtCell) continue;

    let startArrow = "ERone";
    let endArrow = "ERone";
    if (rel.label === "1:N") { startArrow = "ERone"; endArrow = "ERmany"; }
    if (rel.label === "N:1") { startArrow = "ERmany"; endArrow = "ERone"; }
    if (rel.label === "1:1") { startArrow = "ERmandOne"; endArrow = "ERmandOne"; }
    if (rel.label === "M:N") { startArrow = "ERmany"; endArrow = "ERmany"; }

    cells += `    <mxCell id="${edgeId}" value="" style="edgeStyle=orthogonalEdgeStyle;rounded=1;orthogonalLoop=1;jettySize=auto;strokeColor=#95a5a6;fontSize=9;startArrow=${startArrow};endArrow=${endArrow};startFill=0;endFill=0;strokeWidth=1.5;exitX=1;exitY=0.5;exitDx=0;exitDy=0;entryX=0;entryY=0.5;entryDx=0;entryDy=0;dashed=0;" edge="1" parent="1" source="${srcCell}" target="${tgtCell}">\n`;
    cells += `      <mxGeometry relative="1" as="geometry"/>\n`;
    cells += `    </mxCell>\n`;
  }

  return `<?xml version="1.0" encoding="UTF-8"?>
<mxfile host="app.diagrams.net" type="device">
  <diagram name="LRS - E-Absensi Siswa" id="lrs-e-absensi">
    <mxGraphModel dx="1422" dy="762" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="0" pageScale="1" pageWidth="1900" pageHeight="1800" math="0" shadow="0">
      <root>
        <mxCell id="0"/>
        <mxCell id="1" parent="0"/>
${cells}
      </root>
    </mxGraphModel>
  </diagram>
</mxfile>`;
}

// ======================== WRITE FILES ========================
const docsDir = __dirname;

const erdXml = generateERD();
fs.writeFileSync(path.join(docsDir, "ERD_E-Absensi-Siswa.drawio"), erdXml, "utf-8");
console.log("✅ ERD_E-Absensi-Siswa.drawio created!");

const lrsXml = generateLRS();
fs.writeFileSync(path.join(docsDir, "LRS_E-Absensi-Siswa.drawio"), lrsXml, "utf-8");
console.log("✅ LRS_E-Absensi-Siswa.drawio created!");

console.log("\n🎉 Done! Open these files in https://app.diagrams.net");
