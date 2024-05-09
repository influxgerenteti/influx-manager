const IndexedDB = (function () {
  const indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB
  const VERSION = 2
  const connection = {
    db: null
  }

  let isDatabaseReady = false

  if (!indexedDB) {
    console.log("This browser doesn't support IndexedDB")
    return {
    }
  }

  function open (database) {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open(database, VERSION)

      request.onerror = event => reject(event.errorCode)

      request.onupgradeneeded = event => {
        connection.db = event.target.result
        connection.db.createObjectStore('usuarioLogado', {keyPath: 'id'})
        resolve(connection.db)
      }

      request.onsuccess = event => {
        connection.db = event.target.result
        isDatabaseReady = true
        resolve(connection.db)
      }
    })
  }

  function save (store, data) {
    const transaction = connection.db.transaction(store, 'readwrite')
    transaction.objectStore(store).put(data)
  }

  function remove (store, id) {
    connection.db.transaction(store, 'readwrite')
      .objectStore(store)
      .delete(id)
  }

  function retrieve (store, id) {
    return new Promise((resolve, reject) => {
      const result = connection.db.transaction(store)
        .objectStore(store)
        .get(id)

      result.onerror = event => reject(event)
      result.onsuccess = event => resolve(event.target.result)
    })
  }

  function retrieveFirst (store) {
    return new Promise((resolve, reject) => {
      const waitForDatabaseReady = setInterval(() => {
        if (!isDatabaseReady) {
          return
        }

        clearInterval(waitForDatabaseReady)

        const result = connection.db
          .transaction(store)
          .objectStore(store)
          .getAll()

        result.onerror = event => reject(event)

        result.onsuccess = event => {
          const result = event.target.result
          let firstResult = null
          if (result && result.length) {
            firstResult = result[0]
          }

          resolve(firstResult)
        }
      }, 50)
    })
  }

  function clear (store) {
    return new Promise((resolve, reject) => {
      const result = connection.db.transaction(store)
        .objectStore(store)
        .getAll()

      result.onerror = event => reject(event)
      result.onsuccess = event => {
        const res = event.target.result
        res.map(item => IndexedDB.remove(store, item.id))
        resolve()
      }
    })
  }

  return {
    open,
    save,
    remove,
    retrieve,
    retrieveFirst,
    clear
  }
})()

export default IndexedDB
