const jsdom = require('jsdom');
const { JSDOM } = jsdom;

JSDOM.fromURL('http://127.0.0.1:8000/admin/posts/create', {
  runScripts: 'dangerously',
  resources: 'usable'
}).then(dom => {
  dom.window.console.error = (...args) => console.log('JSDOM ERROR:', ...args);
  dom.window.console.log = (...args) => console.log('JSDOM LOG:', ...args);
  
  setTimeout(() => {
    console.log('JSDOM DONE.');
  }, 5000);
}).catch(e => console.error('FAILED TO LOAD:', e));
