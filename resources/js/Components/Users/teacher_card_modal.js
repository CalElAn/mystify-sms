import { ref } from 'vue';

export function useTeacherCardModal() {
  const shouldOpenTeacherCardModal = ref(false);
  const teacherRef = ref({});

  function showTeacherCardModal(teacher) {
    teacherRef.value = teacher;
    shouldOpenTeacherCardModal.value = true;
  }

  return { shouldOpenTeacherCardModal, teacherRef, showTeacherCardModal };
}
