import { Inertia } from '@inertiajs/inertia';

export function getProfilePictureUrl(profilePicturePath) {
  if (
    window.location.hostname === 'demo.mystify-sms.com' ||
    window.location.hostname === 'localhost'
  ) {
    return 'https://i.pravatar.cc/300?u=' + Math.random();
  }

  if (profilePicturePath) {
    if (profilePicturePath.includes('https://')) return profilePicturePath;

    return '/storage/' + profilePicturePath;
  }

  return '';
}

export function changeTerm(termId) {
  let routeData = { termId: termId };

  let urlSearchParams = new URLSearchParams(window.location.search);

  if (urlSearchParams.has('userId'))
    routeData.userId = urlSearchParams.get('userId');

  let queryString = new URLSearchParams(routeData).toString();

  Inertia.get(`${window.location.pathname}?${queryString}`);
}

export function changeAcademicYear(academicYearId) {
  Inertia.get(route('dashboard', { academicYearId: academicYearId }));
}

export function autoGrowTextarea(element) {
  element.target.style.height = '';
  element.target.style.height = element.target.scrollHeight + 'px';
}
