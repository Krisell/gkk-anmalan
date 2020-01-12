<template>
  <div class="action-button-card" :class="{ danger }" @click="$emit('click')">
    <div class="icon">
      <i v-if="icon" class="icon fa" :class="'fa-' + icon"></i>
      <slot v-else></slot>
    </div>
    <div class="description">
      {{ description }}
    </div>
  </div>
</template>

<script>
export default {
  props: ['description', 'icon', 'danger'],
}
</script>

<style scoped lang="less">
.icon {
  min-height: 50px;
  color: #19274a;
}

i.icon {
  align-items: center;
  justify-content: center;
  display: flex;
}

.action-button-card {
  height: 170px;
  background: #FFFFFF;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  border-radius: 2px;
  font-size: 12px;
  cursor: pointer;
  flex: 1;
  margin-right: 10px;
  padding: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #ff9e02;
  color: black;
  border: none;

  -webkit-transition: all .1s ease-in-out;
  transition: all .1s ease-in-out;
  -webkit-animation-duration: .1s;
  animation-duration: .1s;

  &:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 10px rgba(0,0,0,0.20);
  }

  &:active {
    transform: translateY(1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }

  &.danger {
    border: 1px solid #d84a38;
    color: #d84a38;
  }

  border-top: 3px solid #243868;
}

 .action-button-card .description {
   margin-top: 20px;
   color: black;
   text-align: center;
 }

 .action-button-card i {
   font-size: 40px;
 }

 .action-button-card-activated {
   background: #FFFFFF;
   border: 1px solid #45C458;
   color: #45C458;
   box-shadow: 0 2px 4px 0 rgba(0,0,0,0.20);
   border-radius: 2px;
 }

 .action-button-card-danger {
   background: #FFFFFF;
   border: 1px solid #D84A38;
   color: #D84A38;
   box-shadow: 0 2px 4px 0 rgba(0,0,0,0.20);
   border-radius: 2px;
 }
</style>

