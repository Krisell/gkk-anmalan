<template>
  <div v-if="visible" :class="[
    'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 ease-in-out z-50 flex flex-col select-none',
    viewState === 'titlebar' ? 'w-64 h-[40px] cursor-pointer' :
    viewState === 'small' ? 'w-1/3 h-1/3 min-w-[350px] min-h-[300px]' : 
    'w-[96%] h-[96%] bottom-[2%] right-[2%]'
  ]" ref="slideshowContainer">
    <div class="bg-gray-800 text-white p-2 flex justify-between items-center flex-shrink-0 h-[40px]" 
         :class="{ 'cursor-pointer': viewState === 'titlebar' }"
         @click="handleTitlebarClick">
      <div class="text-sm font-medium">GKK Slideshow</div>
      <div class="flex space-x-2">
        <div v-if="viewState === 'titlebar'" class="px-2">
          <i class="fa fa-square-o"></i>
        </div>
        <div v-else class="flex space-x-2">
          <button @click.stop="prevSlide" class="hover:text-gray-300 hover:scale-110 focus:outline-none px-2">
            <i class="fa fa-arrow-left"></i>
          </button>
          <button @click.stop="nextSlide" class="hover:text-gray-300 hover:scale-110 focus:outline-none px-2">
            <i class="fa fa-arrow-right"></i>
          </button>
          <button @click.stop="minimize" class="hover:text-gray-300 hover:scale-110 focus:outline-none px-2">
            <i class="fa fa-minus"></i>
          </button>
          <button @click.stop="toggleView" class="hover:text-gray-300 hover:scale-110 focus:outline-none px-2">
            <i :class="viewState === 'maximized' ? 'fa fa-compress' : 'fa fa-expand'"></i>
          </button>
          <button @click.stop="close" class="hover:text-gray-300 hover:scale-110 focus:outline-none px-2">
            <i class="fa fa-times"></i>
          </button>
        </div>
      </div>
    </div>
    
    <div v-show="viewState !== 'titlebar'" class="w-full flex-grow overflow-hidden">
      <iframe 
        ref="slideshowFrame"
        src="/slides-for-screen" 
        class="w-full h-full border-0 overflow-hidden select-none"
        style="user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none;"
        @click="handleClick"
        @load="setupIframeComm"
      ></iframe>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FloatingSlideshow',
  props: ['user'],
  data() {
    return {
      viewState: 'titlebar', // 'titlebar', 'small', 'maximized'
      iframeLoaded: false
    }
  },
  computed: {
    visible() {
      return this.user && this.user.granted_by != 0
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
    toggleView() {
      if (this.viewState === 'maximized') {
        this.viewState = 'small'
      } else {
        this.viewState = 'maximized'
      }
    },
    handleTitlebarClick() {
      if (this.viewState === 'titlebar') {
        this.viewState = 'small'
      }
    },
    handleClick() {
      // Only expand when clicking on the small slideshow
      if (this.viewState === 'small') {
        this.viewState = 'maximized'
      }
    },
    minimize() {
      this.viewState = 'titlebar'
    },
    close() {
      this.viewState = 'titlebar'
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
