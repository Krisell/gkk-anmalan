const File = {
  save(name, data, type = 'octet/stream') {
    if (navigator.msSaveOrOpenBlob) {
      navigator.msSaveOrOpenBlob(new window.Blob([data], { type }), name)
      return
    }

    let a = document.createElement('a')
    document.body.appendChild(a)
    a.style.display = 'none'
    let blob = new window.Blob([data], { type })
    a.href = window.URL.createObjectURL(blob)
    a.download = name
    a.click()
  },
}

export default File
