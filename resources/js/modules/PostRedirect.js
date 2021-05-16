const PostRedirect = {
  send(options = {}) {
    const form = document.createElement('form')
    form.method = 'post'
    form.action = options.url

    if (options.open) {
      form.target = 'gkk-tab'
    }

    form.style.display = 'none'

    Object.entries(options.data).forEach(([key, value]) => {
      const input = document.createElement('input')
      input.name = key
      input.value = value
      form.appendChild(input)
    })

    document.body.appendChild(form)

    if (options.open) {
      window.open('', 'gkk-tab')
    }

    form.submit()
  },
}

export default PostRedirect
