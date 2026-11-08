<template>
  <div class="rich-text-editor" :class="{ focused }">
    <div v-if="showToolbar && editable && editor" class="toolbar" @mousedown.prevent>
      <button
        v-for="button in buttons"
        :key="button.title"
        type="button"
        class="toolbar-button"
        :class="{ active: button.active?.() }"
        :title="button.title"
        :disabled="button.disabled?.()"
        @click="button.run"
      >
        <i class="fa" :class="`fa-${button.icon}`"></i>
      </button>
      <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="pickedImage" />
    </div>

    <EditorContent :editor="editor" class="content prose prose-sm max-w-none" />

    <button
      v-if="editable"
      type="button"
      class="toolbar-toggle"
      :class="{ active: showToolbar }"
      :aria-pressed="showToolbar"
      :title="showToolbar ? 'Dölj formatering' : 'Visa formatering'"
      @mousedown.prevent
      @click="showToolbar = !showToolbar"
    >
      Aa
    </button>
  </div>
</template>

<script setup>
import { onBeforeUnmount, ref, useTemplateRef, watch } from 'vue'
import { EditorContent, useEditor } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import { Placeholder } from '@tiptap/extensions'
import FirebaseFileUpload from '../../modules/FirebaseFileUpload.js'

// A Tiptap editor bound with v-model (HTML). Only simple formatting: bold, italic, underline, lists, links and images.
// Pasted, dropped or picked images are uploaded to Firebase storage and kept as links in the HTML.
const {
  modelValue = '',
  jwt,
  placeholder = '',
  minHeight = '96px',
  editable = true,
} = defineProps({
  editable: { type: Boolean, default: true },
  modelValue: String,
  jwt: String,
  placeholder: String,
  minHeight: String,
})

const emit = defineEmits(['update:modelValue'])

// The toolbar is hidden until asked for, so the text reads like the finished minutes. Keyboard shortcuts (⌘B, ⌘I,
// ⌘U) and pasting images work either way.
const showToolbar = ref(false)
const focused = ref(false)
const fileInput = useTemplateRef('fileInput')

const imageFiles = (files) => Array.from(files || []).filter((file) => file.type.startsWith('image/'))

async function insertImages(files, position) {
  for (const file of files) {
    const extension = (file.name.split('.').pop() || 'png').toLowerCase()
    const url = await FirebaseFileUpload.upload(jwt, file, extension)
    const chain = editor.value.chain().focus()
    ;(position === undefined ? chain : chain.setTextSelection(position)).setImage({ src: url }).run()
  }
}

const editor = useEditor({
  content: modelValue,
  editable,
  extensions: [
    StarterKit.configure({
      heading: false,
      code: false,
      codeBlock: false,
      blockquote: false,
      strike: false,
      horizontalRule: false,
      link: { openOnClick: false, autolink: true, defaultProtocol: 'https' },
    }),
    Image,
    Placeholder.configure({ placeholder }),
  ],
  editorProps: {
    attributes: { style: `min-height: ${minHeight}` },
    handlePaste(view, event) {
      const files = imageFiles(event.clipboardData?.files)
      if (files.length === 0) {
        return false
      }
      insertImages(files)
      return true
    },
    handleDrop(view, event, slice, moved) {
      const files = moved ? [] : imageFiles(event.dataTransfer?.files)
      if (files.length === 0) {
        return false
      }
      event.preventDefault()
      const position = view.posAtCoords({ left: event.clientX, top: event.clientY })?.pos
      insertImages(files, position)
      return true
    },
  },
  onUpdate: ({ editor }) => emit('update:modelValue', editor.isEmpty ? '' : editor.getHTML()),
  onFocus: () => (focused.value = true),
  onBlur: () => (focused.value = false),
})

onBeforeUnmount(() => editor.value?.destroy())

watch(
  () => editable,
  (value) => editor.value?.setEditable(value),
)

const chain = () => editor.value.chain().focus()

function setLink() {
  const previous = editor.value.getAttributes('link').href
  const url = window.prompt('Länk', previous || 'https://')
  if (url === null) {
    return
  }
  if (url.trim() === '' || url === 'https://') {
    chain().extendMarkRange('link').unsetLink().run()
    return
  }
  chain().extendMarkRange('link').setLink({ href: url.trim() }).run()
}

function pickedImage(event) {
  insertImages(imageFiles(event.target.files))
  event.target.value = ''
}

const buttons = [
  {
    icon: 'bold',
    title: 'Fetstil (⌘B)',
    run: () => chain().toggleBold().run(),
    active: () => editor.value.isActive('bold'),
  },
  {
    icon: 'italic',
    title: 'Kursiv (⌘I)',
    run: () => chain().toggleItalic().run(),
    active: () => editor.value.isActive('italic'),
  },
  {
    icon: 'underline',
    title: 'Understruken (⌘U)',
    run: () => chain().toggleUnderline().run(),
    active: () => editor.value.isActive('underline'),
  },
  {
    icon: 'list-ul',
    title: 'Punktlista',
    run: () => chain().toggleBulletList().run(),
    active: () => editor.value.isActive('bulletList'),
  },
  {
    icon: 'list-ol',
    title: 'Numrerad lista',
    run: () => chain().toggleOrderedList().run(),
    active: () => editor.value.isActive('orderedList'),
  },
  { icon: 'link', title: 'Länk', run: setLink, active: () => editor.value.isActive('link') },
  { icon: 'picture-o', title: 'Infoga bild', run: () => fileInput.value.click() },
  { icon: 'undo', title: 'Ångra', run: () => chain().undo().run(), disabled: () => !editor.value.can().undo() },
  { icon: 'repeat', title: 'Gör om', run: () => chain().redo().run(), disabled: () => !editor.value.can().redo() },
]
</script>

<style>
.rich-text-editor {
  position: relative;
  border: 1px solid rgb(209 213 219);
  border-radius: 0.375rem;
  background: #fff;
  transition: border-color 150ms;
}
.rich-text-editor:hover {
  border-color: rgb(156 163 175);
}
.rich-text-editor.focused {
  border-color: var(--color-gkk);
}
.rich-text-editor .toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 2px;
  padding: 4px;
  border-bottom: 1px solid rgb(229 231 235);
}
.rich-text-editor .toolbar-button {
  width: 2rem;
  height: 2rem;
  border-radius: 0.25rem;
  color: rgb(75 85 99);
  cursor: pointer;
}
.rich-text-editor .toolbar-button:hover:not(:disabled) {
  background: rgb(243 244 246);
}
.rich-text-editor .toolbar-button.active {
  background: rgb(229 231 235);
  color: var(--color-gkk);
}
.rich-text-editor .toolbar-button:disabled {
  opacity: 0.3;
  cursor: default;
}
.rich-text-editor .tiptap {
  padding: 0.5rem 2.5rem 0.5rem 0.75rem;
  outline: none;
}
.rich-text-editor .tiptap > :first-child {
  margin-top: 0;
}
.rich-text-editor .tiptap > :last-child {
  margin-bottom: 0;
}
.rich-text-editor .tiptap img {
  max-width: 100%;
  height: auto;
}
.rich-text-editor .tiptap img.ProseMirror-selectednode {
  outline: 2px solid var(--color-gkk);
}
.rich-text-editor .tiptap p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  height: 0;
  color: rgb(156 163 175);
  pointer-events: none;
}
.rich-text-editor .toolbar-toggle {
  position: absolute;
  right: 0.375rem;
  bottom: 0.375rem;
  width: 1.75rem;
  height: 1.75rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: rgb(156 163 175);
  cursor: pointer;
}
.rich-text-editor .toolbar-toggle:hover,
.rich-text-editor .toolbar-toggle.active {
  background: rgb(243 244 246);
  color: var(--color-gkk);
}
</style>
