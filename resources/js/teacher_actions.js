export const teacherActions = [
  {
    label: 'Classes',
    href: route('class_teacher.form'),
    component: 'ClassTeacher/Form',
  },
  {
    label: 'Students',
    href: route('class_student.form'),
    component: 'ClassStudent/Form',
  },
  {
    label: 'Grades',
    href: route('grades.form'),
    component: 'Grades/Form',
  },
]
