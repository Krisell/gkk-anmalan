<template>
  <div v-if="visible" :class="[
    'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 ease-in-out z-50 flex flex-col',
    isMinimized ? 'w-1/3 h-1/3 min-w-[400px] min-h-[300px] cursor-pointer' : 'w-[96%] h-[96%] bottom-[2%] right-[2%]'
  ]" ref="slideshowContainer">
    <div class="bg-gray-800 text-white p-2 flex justify-between items-center flex-shrink-0 h-[40px]">
      <div class="text-sm font-medium">GKK Slideshow</div>
      <div class="flex space-x-2">
        <button @click="prevSlide" class="hover:text-gray-300 hover:scale-110 focus:outline-none px-2">
          <i class="fa fa-arrow-left"></i>
        </button>
        <button @click="nextSlide" class="hover:text-gray-300 hover:scale-110 focus:outline-none px-2">
          <i class="fa fa-arrow-right"></i>
        </button>
        <button @click="toggleMinimize" class="hover:text-gray-300 hover:scale-110 focus:outline-none px-2">
          <i :class="isMinimized ? 'fa fa-expand' : 'fa fa-compress'"></i>
        </button>
        <button @click="close" class="hover:text-gray-300 hover:scale-110 focus:outline-none px-2">
          <i class="fa fa-times"></i>
        </button>
      </div>
    </div>
    
    <div class="w-full flex-grow overflow-hidden">
      <iframe 
        ref="slideshowFrame"
        src="/slides-for-screen" 
        class="w-full h-full border-0 overflow-hidden"
        @click="handleClick"
        @load="setupIframeComm"
      ></iframe>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FloatingSlideshow',
  data() {
    return {
      isMinimized: true,
      visible: true,
      iframeLoaded: false
    }
  },
  mounted() {
    // Listen for messages from the iframe
    window.addEventListener('message', this.handleIframeMessage)
  },
  beforeUnmount() {
    // Clean up event listeners
    window.removeEventListener('message', this.handleIframeMessage)
  },
  methods: {
    setupIframeComm() {
      this.iframeLoaded = true
    },
    handleIframeMessage(event) {
      // Handle messages from the iframe if needed
      if (event.data && event.data.type === 'slideshow-message') {
        console.log('Message from slideshow:', event.data.content)
      }
    },
    toggleMinimize() {
      this.isMinimized = !this.isMinimized
    },
    handleClick() {
      // Only expand when clicking on the minimized slideshow
      if (this.isMinimized) {
        this.isMinimized = false
      }
    },
    close() {
      this.visible = false
      // No localStorage - closing will only last until page refresh
    },
    nextSlide() {
      if (this.iframeLoaded) {
        this.$refs.slideshowFrame.contentWindow.postMessage({
          type: 'slideshow-control',
          action: 'next'
        }, '*')
      }
    },
    prevSlide() {
      if (this.iframeLoaded) {
        this.$refs.slideshowFrame.contentWindow.postMessage({
          type: 'slideshow-control',
          action: 'previous'
        }, '*')
      }
    }
  }
}
</script>
