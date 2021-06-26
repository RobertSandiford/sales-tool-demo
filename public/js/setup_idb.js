window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB

var dbHolder = {};
var db
var local_users
var local_quotes
var local_quote_items

var dbReq = indexedDB.open('tgd-sales-tool', 2);
dbReq.onupgradeneeded = function(event) {
    console.log("indexDB updatedneeded")

    // Set the db variable to our database so we can use it!  
    db = event.target.result;

    // Create stores
    local_users = db.createObjectStore('local_users', {autoIncrement: true})
    local_users.createIndex('username', 'username', {unique : false})
    
    local_quotes = db.createObjectStore('local_quotes', {autoIncrement: true});
    local_quotes.createIndex('uploaded', 'uploaded')

    local_quote_items = db.createObjectStore('local_quote_items', {autoIncrement: true});
    local_quote_items.createIndex('quote_id', 'quote_id', {unique : false})
    local_quote_items.createIndex('product', 'product', {unique : false})
}

dbReq.onsuccess = function(event) {
    db = event.target.result;
}

dbReq.onerror = function(event) {
    alert('error opening database ' + event.target.errorCode);
}