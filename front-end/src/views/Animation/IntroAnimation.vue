<script setup>
import { ref, onMounted } from 'vue'
import { gsap } from 'gsap'

import park1 from '@/assets/park-11.png'
import park2 from '@/assets/park-22.png'

const emit = defineEmits(['finished'])

const leftImage = ref(null)
const rightImage = ref(null)

onMounted(() => {
  const tl = gsap.timeline({
    onComplete: () => {
      emit('finished')
    }
  })

  tl.from([leftImage.value, rightImage.value], {
    y: 300,
    opacity: 0,
    duration: 1.2,
    ease: "power3.out",
    stagger: 0.2
  })

  tl.to({}, { duration: 0.5 })

  // 1
//   tl.to(rightImage.value, {
//     x: -200,
//     scale: 0.9,
//     duration: 1,
//     ease: "power2.inOut"
//   })

tl.to(rightImage.value, {
  x: -200,
  scale: 0.9,
  duration: 1.2,
  ease: "power2.inOut"
})

tl.to(rightImage.value, {
  opacity: 0,
  duration: 1,
  ease: "power2.out"
}, "-=1")


//   tl.to([leftImage.value, rightImage.value], {
//     scale: 1.5,
//     duration: 0.8
//   })

tl.to(rightImage.value, {
  x: -200,
  scale: 0.9,
  duration: 1.2,
  ease: "power2.inOut"
})

tl.to(rightImage.value, {
  opacity: 0,
  duration: 0.8,
  ease: "power2.out"
}, "-=1")

tl.to(leftImage.value, {
  x: 120, 
  scale: 3,
  duration: 1.3,
  ease: "power3.inOut"
})

})
</script>

<template>
  <div class="intro-wrapper">
    <div class="images">
      <img ref="leftImage" :src="park1" class="img-1" />
      <img ref="rightImage" :src="park2" class="img-2" />
    </div>
  </div>
</template>

<style scoped>

.intro-wrapper {
  position: fixed;
  inset: 0;
  background: #2e00adff;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.images {
  display: flex;
  gap: 40px;
}

/* .img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.2);
} */

.img-1 {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}
.img-2 {
  width: 230px;
  height: 100px;
  object-fit: cover;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}
</style>