<template>
  <div class="page-content error-404">
    <div class="page-pad">
      <h1>Wow! 500 server error</h1>
      <p>We are not perfect yet :)</p>
      <p>Please check again later.</p>
      <router-link to="/">BACK</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NoFound',
  data() {
    return {}
  }
}
</script>

<style lang="scss">
</style>
