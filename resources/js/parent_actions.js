export const parentActions = [
  {
    label: 'Add child',
    href: route('add_as_child_request.form'),
    component: 'AddAsChildRequestForm',
  },
  {
    label: 'Remove child',
    href: route('remove_children.form'),
    component: 'RemoveChildrenForm',
  },
];
