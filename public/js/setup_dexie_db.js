import Dexie from 'dexie';


console.log("try dexie db");

var dexieDb = new Dexie("sales-database");

/*dexieDb.version(3).stores({
    local_users : 'id,username',
    local_quotes : 'id,uploaded',
    local_quote_items : 'id,quote_id,product'
});*/

dexieDb.version(3).stores({
    test_table : 'id,name',
});