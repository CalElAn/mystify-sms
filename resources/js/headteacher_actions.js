export const headteacherActions = [
  {
    label: 'Academic years',
    href: route('academic_years.form'),
    component: 'AcademicYears/Form',
  },
  {
    label: 'Terms',
    href: route('terms.form'),
    component: 'Terms/Form',
  },
  {
    label: 'Classes',
    href: route('classes.form'),
    component: 'Classes/Form',
  },
  {
    label: 'Review requests to join school',
    href: route('notifications.view_school_notifications'),
    component: 'Notifications/SchoolNotifications',
  },
  // {
  //   label: 'Add school fees',
  //   name: '',
  //   href: '#',
  // },
  {
    label: 'Add message to notice board',
    href: route('notice_board.create'),
    component: 'NoticeBoard/Create',
  },
];
