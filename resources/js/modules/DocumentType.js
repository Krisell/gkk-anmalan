import { File, FileImage, FileSpreadsheet, FileText, Link, Presentation } from 'lucide-vue-next'

const TYPES = [
  { extensions: ['pdf'], label: 'PDF', icon: FileText, classes: 'bg-red-50 text-red-600' },
  {
    extensions: ['doc', 'docx', 'pages', 'odt', 'rtf'],
    label: 'Word',
    icon: FileText,
    classes: 'bg-blue-50 text-blue-600',
  },
  {
    extensions: ['xls', 'xlsx', 'numbers', 'ods', 'csv'],
    label: 'Kalkylark',
    icon: FileSpreadsheet,
    classes: 'bg-green-50 text-green-700',
  },
  {
    extensions: ['ppt', 'pptx', 'key', 'odp'],
    label: 'Presentation',
    icon: Presentation,
    classes: 'bg-orange-50 text-orange-600',
  },
  {
    extensions: ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic'],
    label: 'Bild',
    icon: FileImage,
    classes: 'bg-purple-50 text-purple-600',
  },
]

/**
 * What kind of document a URL points at, for its icon and a short label. Uploaded files live in Firebase Storage,
 * where the file name (and so the extension) is URL-encoded into the path; published minutes are pages on this site.
 */
export function documentType(url) {
  let parsed
  try {
    parsed = new URL(url, window.location.origin)
  } catch {
    return { label: 'Länk', icon: Link, classes: 'bg-gray-100 text-gray-500' }
  }

  if (parsed.origin === window.location.origin && parsed.pathname.startsWith('/member-documents/minutes/')) {
    return { label: 'Protokoll', icon: FileText, classes: 'bg-gkk/5 text-gkk' }
  }

  let path = parsed.pathname
  try {
    path = decodeURIComponent(path)
  } catch {
    // Keep the encoded path.
  }
  const fileName = path.split('/').pop()
  const extension = fileName.includes('.') ? fileName.split('.').pop().toLowerCase() : ''

  const type = TYPES.find((t) => t.extensions.includes(extension))
  if (type) {
    return type
  }

  if (extension && extension.length <= 5 && parsed.hostname.includes('firebasestorage')) {
    return { label: extension.toUpperCase(), icon: File, classes: 'bg-gray-100 text-gray-500' }
  }

  return { label: parsed.hostname.replace(/^www\./, '') || 'Länk', icon: Link, classes: 'bg-gray-100 text-gray-500' }
}

/** Whether a folder or one of its documents matches a search, ignoring case. */
export function matchesSearch(text, query) {
  return text.toLocaleLowerCase('sv').includes(query.toLocaleLowerCase('sv'))
}
