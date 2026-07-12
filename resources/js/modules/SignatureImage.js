const SignatureImage = {
  // Crop away transparent margins so the PNG aspect-fits cleanly when stamped.
  trimCanvas(canvas) {
    const ctx = canvas.getContext('2d')
    const { width, height } = canvas
    const data = ctx.getImageData(0, 0, width, height).data

    let minX = width
    let minY = height
    let maxX = -1
    let maxY = -1

    for (let y = 0; y < height; y++) {
      for (let x = 0; x < width; x++) {
        if (data[(y * width + x) * 4 + 3] > 0) {
          if (x < minX) minX = x
          if (x > maxX) maxX = x
          if (y < minY) minY = y
          if (y > maxY) maxY = y
        }
      }
    }

    if (maxX < minX) {
      return null
    }

    const padding = 4
    minX = Math.max(0, minX - padding)
    minY = Math.max(0, minY - padding)
    maxX = Math.min(width - 1, maxX + padding)
    maxY = Math.min(height - 1, maxY + padding)

    const trimmed = document.createElement('canvas')
    trimmed.width = maxX - minX + 1
    trimmed.height = maxY - minY + 1
    trimmed
      .getContext('2d')
      .drawImage(canvas, minX, minY, trimmed.width, trimmed.height, 0, 0, trimmed.width, trimmed.height)

    return trimmed.toDataURL('image/png')
  },

  async renderTypedSignature(name) {
    if (!name.trim()) {
      return null
    }

    const font = "64px 'Dancing Script', cursive"
    await document.fonts.load(font)

    const canvas = document.createElement('canvas')
    const ctx = canvas.getContext('2d')
    ctx.font = font
    canvas.width = Math.ceil(ctx.measureText(name).width) + 40
    canvas.height = 96

    // Resizing a canvas resets its context state.
    const ctx2 = canvas.getContext('2d')
    ctx2.font = font
    ctx2.fillStyle = '#1e3a5f'
    ctx2.fillText(name, 20, 68)

    return this.trimCanvas(canvas)
  },
}

export default SignatureImage
