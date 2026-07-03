const puppeteer = require('puppeteer');

// Usage: node scraper.js <followerUsername> <targetUsername>
const followerUsername = process.argv[2];
const targetUsername = process.argv[3];

const IG_USERNAME = process.env.IG_USERNAME || 'your_burner_account';
const IG_PASSWORD = process.env.IG_PASSWORD || 'your_burner_password';

if (!followerUsername || !targetUsername) {
    console.error('Missing arguments');
    process.exit(1);
}

(async () => {
    let browser = null;
    try {
        browser = await puppeteer.launch({
            headless: 'new',
            args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-notifications']
        });
        const page = await browser.newPage();
        
        // Go to login
        await page.goto('https://www.instagram.com/accounts/login/', { waitUntil: 'networkidle2' });
        
        // Check if already logged in (cookies) or need to login
        const usernameInput = await page.$('input[name="username"]');
        if (usernameInput) {
            await page.type('input[name="username"]', IG_USERNAME, { delay: 50 });
            await page.type('input[name="password"]', IG_PASSWORD, { delay: 50 });
            await page.click('button[type="submit"]');
            await page.waitForNavigation({ waitUntil: 'networkidle2' });
        }

        // Navigate to target user profile
        await page.goto(`https://www.instagram.com/${targetUsername}/`, { waitUntil: 'networkidle2' });
        
        // Check if user exists / page not found
        const notFound = await page.evaluate(() => {
            return document.body.innerText.includes("Sorry, this page isn't available.");
        });
        if (notFound) {
            console.log('FOLLOWING_NO_TARGET_NOT_FOUND');
            await browser.close();
            return;
        }

        // Click on "followers" link
        const followersLink = await page.$(`a[href="/${targetUsername}/followers/"]`);
        if (!followersLink) {
            console.log('FOLLOWING_UNKNOWN_CANNOT_FIND_FOLLOWERS_LINK');
            await browser.close();
            return;
        }
        
        await followersLink.click();
        
        // Wait for modal to load
        await page.waitForSelector('div[role="dialog"]', { timeout: 10000 });
        
        // Type in the search box inside the followers modal
        const searchInput = await page.$('input[placeholder="Search"]');
        if (searchInput) {
            await searchInput.type(followerUsername, { delay: 100 });
            await new Promise(r => setTimeout(r, 2000)); // wait for search results
            
            // Check if the username appears in the list
            const found = await page.evaluate((follower) => {
                const elements = document.querySelectorAll('span');
                for (let el of elements) {
                    if (el.innerText === follower) return true;
                }
                return false;
            }, followerUsername);

            if (found) {
                console.log('FOLLOWING_YES');
            } else {
                console.log('FOLLOWING_NO');
            }
        } else {
            console.log('FOLLOWING_UNKNOWN_NO_SEARCH_BOX');
        }

    } catch (err) {
        console.error('ERROR:', err.message);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
})();
