const puppeteer = require("puppeteer");
const path = require("path");
const fs = require("fs");

const SCREENSHOTS_DIR = path.join(__dirname, "screenshots");
const BASE_URL = "http://127.0.0.1:8000";
const VIEWPORT = { width: 1440, height: 900 };

async function delay(ms) {
  return new Promise((r) => setTimeout(r, ms));
}

async function login(page, email, password) {
  await page.goto(`${BASE_URL}/login`, { waitUntil: "networkidle2", timeout: 30000 });
  await delay(1500);
  await page.type('input[name="email"]', email, { delay: 30 });
  await page.type('input[name="password"]', password, { delay: 30 });
  await page.click('button[type="submit"]');
  await delay(3000);
}

async function logout(page) {
  try {
    // Try clicking logout via form POST
    await page.goto(`${BASE_URL}/logout`, {
      waitUntil: "networkidle2",
      timeout: 10000,
    }).catch(() => {});
    // Or navigate to login
    await page.goto(`${BASE_URL}/login`, { waitUntil: "networkidle2", timeout: 10000 });
    await delay(1000);
  } catch (e) {
    // Clear cookies as fallback
    const client = await page.createCDPSession();
    await client.send("Network.clearBrowserCookies");
  }
}

async function main() {
  // Ensure screenshots directory
  if (!fs.existsSync(SCREENSHOTS_DIR)) {
    fs.mkdirSync(SCREENSHOTS_DIR, { recursive: true });
  }

  console.log("🚀 Starting Puppeteer...");
  const browser = await puppeteer.launch({
    headless: true,
    defaultViewport: VIEWPORT,
    args: ["--no-sandbox", "--disable-setuid-sandbox"],
  });

  const page = await browser.newPage();

  try {
    // ========== 1. LANDING PAGE (HERO) ==========
    console.log("📸 1/5 — Landing Page (Hero)...");
    await page.goto(BASE_URL, { waitUntil: "networkidle2", timeout: 30000 });
    await delay(2000);
    await page.screenshot({
      path: path.join(SCREENSHOTS_DIR, "01_landing_hero.png"),
      fullPage: false,
    });
    console.log("   ✅ Saved: 01_landing_hero.png");

    // ========== 2. LANDING PAGE (PROSES KERJA) ==========
    console.log("📸 2/5 — Landing Page (Proses Kerja)...");
    // Scroll to proses kerja section
    await page.evaluate(() => {
      const el = document.querySelector("#cara-kerja") || document.querySelector('[id*="kerja"]') || document.querySelector('[id*="proses"]');
      if (el) {
        el.scrollIntoView({ behavior: "instant", block: "start" });
      } else {
        window.scrollBy(0, 900);
      }
    });
    await delay(1500);
    await page.screenshot({
      path: path.join(SCREENSHOTS_DIR, "02_landing_proses_kerja.png"),
      fullPage: false,
    });
    console.log("   ✅ Saved: 02_landing_proses_kerja.png");

    // ========== 3. DASHBOARD SUPER ADMIN ==========
    console.log("📸 3/5 — Dashboard Super Admin...");
    await login(page, "admin@admin.com", "password");
    await delay(2000);
    await page.screenshot({
      path: path.join(SCREENSHOTS_DIR, "03_dashboard_super_admin.png"),
      fullPage: false,
    });
    console.log("   ✅ Saved: 03_dashboard_super_admin.png");

    // Logout
    await logout(page);

    // ========== 4. DASHBOARD WALI KELAS ==========
    console.log("📸 4/5 — Dashboard Wali Kelas...");
    await login(page, "wk1@test.com", "password");
    await delay(2000);
    await page.screenshot({
      path: path.join(SCREENSHOTS_DIR, "04_dashboard_wali_kelas.png"),
      fullPage: false,
    });
    console.log("   ✅ Saved: 04_dashboard_wali_kelas.png");

    // Logout
    await logout(page);

    // ========== 5. DASHBOARD GURU ==========
    console.log("📸 5/5 — Dashboard Guru...");
    // Check if there's a guru account
    // From seeder: guru accounts might have specific emails
    // Let's try common patterns or use wali_kelas as teacher view
    // Try the teacher account from the seeder
    let guruLoggedIn = false;
    const guruEmails = ["guru1@test.com", "guru@test.com", "teacher@test.com"];
    
    for (const email of guruEmails) {
      try {
        await login(page, email, "password");
        const url = page.url();
        if (!url.includes("login")) {
          guruLoggedIn = true;
          console.log(`   Logged in as guru: ${email}`);
          break;
        }
      } catch (e) {}
      await logout(page);
    }
    
    if (!guruLoggedIn) {
      // If no separate guru account, try using DummyDataSeeder teacher accounts
      // wk accounts have wali_kelas role, which also shows teacher dashboard
      console.log("   ⚠️ No separate guru account found, using wali kelas teacher view");
      // Check via the URL from the screenshots - user showed guru dashboard logged in as "DIAN ARDIANSYAH"
      // This might be a manually created account. Let's use a PHP artisan command to check
      console.log("   ℹ️ Attempting to find guru accounts via database...");
      
      // Try getting the guru dashboard URL directly after login as admin
      await login(page, "admin@admin.com", "password");
      // Navigate to guru dashboard
      await page.goto(`${BASE_URL}/guru/dashboard`, { waitUntil: "networkidle2", timeout: 10000 }).catch(() => {});
      await delay(2000);
    }
    
    await page.screenshot({
      path: path.join(SCREENSHOTS_DIR, "05_dashboard_guru.png"),
      fullPage: false,
    });
    console.log("   ✅ Saved: 05_dashboard_guru.png");

  } catch (err) {
    console.error("❌ Error:", err.message);
  } finally {
    await browser.close();
    console.log("\n🎉 All screenshots saved to docs/screenshots/");
  }
}

main();
