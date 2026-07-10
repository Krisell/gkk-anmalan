// Converts between overlay CSS pixels (origin top-left) and PDF points in the
// page's unrotated user space (origin bottom-left) — the space pdf-lib draws in.
// Going through the pdf.js viewport keeps pages with /Rotate correct.
const PdfCoordinates = {
  cssRectToPdf(viewport, rect) {
    const [ax, ay] = viewport.convertToPdfPoint(rect.left, rect.top)
    const [bx, by] = viewport.convertToPdfPoint(rect.left + rect.width, rect.top + rect.height)

    return {
      x: Math.min(ax, bx),
      y: Math.min(ay, by),
      width: Math.abs(bx - ax),
      height: Math.abs(by - ay),
    }
  },

  pdfRectToCss(viewport, field) {
    const [ax, ay] = viewport.convertToViewportPoint(field.x, field.y)
    const [bx, by] = viewport.convertToViewportPoint(+field.x + +field.width, +field.y + +field.height)

    return {
      left: Math.min(ax, bx),
      top: Math.min(ay, by),
      width: Math.abs(bx - ax),
      height: Math.abs(by - ay),
    }
  },
}

export default PdfCoordinates
