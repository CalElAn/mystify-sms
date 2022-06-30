export const studentActions = [
  {
    label: 'Join class',
    href: route('class_student.join_class.form'),
    component: 'ClassStudent/JoinClass/Form',
  },
  {
    label: 'Add parent',
    href: route('add_as_parent_request.form'),
    component: 'AddAsParentRequestForm',
  },
];
