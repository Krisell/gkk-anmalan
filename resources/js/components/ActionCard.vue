<template>
  <div class="action-button-card" :class="{ danger }" @click="$emit('click')">
    <div v-if="unanswered > 0" class="unanswered">
      {{ unanswered }}
      <div class="unanswered inside"></div>
    </div>
    <div class="icon">
      <i v-if="icon" class="icon fa" :class="'fa-' + icon"></i>
      <slot v-else></slot>
    </div>
    <div class="description" v-html="description"></div>
    <div style="position: absolute; left: -8px; bottom: -8px;" @click.stop="$emit('admin')" v-if="admin">
      <el-button danger style="padding: 5px; font-size: 10px;">Admin</el-button>
    </div>
  </div>
</template>

<script>
export default {
  props: ['description', 'icon', 'danger', 'admin', 'unanswered'],
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
  position: relative;
  height: 170px;
  background: #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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

  -webkit-transition: all 0.1s ease-in-out;
  transition: all 0.1s ease-in-out;
  -webkit-animation-duration: 0.1s;
  animation-duration: 0.1s;

  &:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
  }

  &:active {
    transform: translateY(1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
  background: #ffffff;
  border: 1px solid #45c458;
  color: #45c458;
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2);
  border-radius: 2px;
}

.action-button-card-danger {
  background: #ffffff;
  border: 1px solid #d84a38;
  color: #d84a38;
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2);
  border-radius: 2px;
}

.unanswered {
  position: absolute;
  top: -9px;
  right: -7px;
  background: #243868;
  color: #f9fafc;
  border: 1px solid #243868;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  font-size: 19px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.inside {
  position: absolute;
  left: 0px;
  top: 0px;
  border-color: white;
  z-index: 999;
  width: 26px;
  height: 26px;
  background: none;
  color: none;
  border-width: 1px;
}
</style>
