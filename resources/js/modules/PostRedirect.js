const PostRedirect = {
  send (options = {}) {
    const form = document.createElement('form')
    form.method = 'post'
    form.action = options.url
    form.style.display = 'none'

    Object.entries(options.data).forEach(([key, value]) => {
      const input = document.createElement('input')
      input.name = key
      input.value = value
      form.appendChild(input)
    })

    document.body.appendChild(form)
    form.submit()
  },
}

export default PostRedirect
