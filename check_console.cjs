const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({ headless: 'new' });
  const page = await browser.newPage();
  
  page.on('console', msg => console.log('PAGE LOG:', msg.text()));
  page.on('pageerror', error => console.log('PAGE ERROR:', error.message));
  page.on('response', response => {
    if (response.status() >= 400) {
      console.log('HTTP ERROR:', response.status(), response.url());
    }
  });

  try {
    console.log('Navigating to login...');
    await page.goto('http://127.0.0.1:8000/login');
    
    console.log('Filling login form...');
    await page.type('input[name="email"]', 'admin@ffinsta.com');
    await page.type('input[name="password"]', 'Admin@123456');
    await Promise.all([
      page.waitForNavigation(),
      page.click('button[type="submit"]')
    ]);

    console.log('Navigating to create post...');
    await page.goto('http://127.0.0.1:8000/admin/posts/create');
    
    console.log('Waiting for a bit...');
    await new Promise(r => setTimeout(r, 5000));
    
    console.log('Done.');
  } catch (err) {
    console.error('SCRIPT ERROR:', err);
  } finally {
    await browser.close();
  }
})();
