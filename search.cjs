const https = require('https');
https.get('https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest', (res) => {
    let data = '';
    res.on('data', (c) => data += c);
    res.on('end', () => {
        const matches = data.match(/class(?:Name)?=[\"'](cdx-[a-zA-Z0-9\-\_]+|ce-popover-[a-zA-Z0-9\-\_]+)/g);
        console.log(matches ? [...new Set(matches)].join('\n') : 'no matches');
    });
});
