<template>
  <div class="container mx-auto max-w-3xl">
    <h1 class="text-center text-3xl font-hairline mb-6">Klubbrekord</h1>

    <h2 class="text-center text-2xl font-hairline mb-6">
      Denna sektion är under utveckling. Klubbrekorden finns för tillfället på
      https://www.gkk-styrkelyft.se/klubbrekord/
    </h2>
    <div class="text-center">
      {{ results }}
    </div>

    <GkkLink to="/" text="Tillbaka till startsidan" />
  </div>
</template>

<script>
export default {
  props: ['results'],
}
</script>
