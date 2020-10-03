import Random from '@krisell/random'

const FirebaseFileUpload = {
  async upload(jwt, data, extension) {
    await window.firebase.auth().signInWithCustomToken(jwt)

    const storage = window.firebase.storage()

    return new Promise((resolve, reject) => {
      return storage
        .ref()
        .child(`/uploaded/${Random.randomString(30)}.${extension}`)
        .put(data, { cacheControl: 'public, max-age=31536000' })
        .then((file) =>
          file.ref.getDownloadURL().then((url) => {
            resolve(url)
          }),
        )
    })
  },
}

export default FirebaseFileUpload
