// How the competition cards are laid out on an A4 page. A card is drawn 95 mm wide and about 89 mm high; on a page
// with fewer cards it is made larger (zoom) to fill its share of the page, and wider, so the boxes to write in grow.

const MARGIN_X = 7
const MARGIN_Y = 8
const GAP_X = 6
const GAP_Y = 5
const CARD_WIDTH = 95
// A little more than the card's height, so a card never spills over onto a page of its own.
const CARD_HEIGHT = 89

const SHEETS = { portrait: [210, 297], landscape: [297, 210] }

function layout(perPage, orientation, columns, label) {
  const rows = perPage / columns
  const [pageWidth, pageHeight] = SHEETS[orientation]
  const cellWidth = (pageWidth - 2 * MARGIN_X - GAP_X * (columns - 1)) / columns
  const cellHeight = (pageHeight - 2 * MARGIN_Y - GAP_Y * (rows - 1)) / rows
  // As large as the height allows, but never narrower than the card is drawn.
  const zoom = Math.min(cellHeight / CARD_HEIGHT, cellWidth / CARD_WIDTH)
  return { perPage, orientation, columns, label, zoom, width: cellWidth / zoom }
}

export const CARD_LAYOUTS = [
  layout(6, 'portrait', 2, '6 per sida · stående'),
  layout(4, 'landscape', 2, '4 per sida · liggande'),
  layout(2, 'portrait', 1, '2 per sida · stående'),
]

export function cardLayout(perPage) {
  return CARD_LAYOUTS.find((entry) => entry.perPage === Number(perPage)) ?? CARD_LAYOUTS[0]
}
