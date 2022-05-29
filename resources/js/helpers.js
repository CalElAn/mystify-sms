import { Inertia } from '@inertiajs/inertia';

export function getProfilePictureUrl(profilePicturePath) {
  if (profilePicturePath) {
    if (profilePicturePath.includes('https://'))
      return profilePicturePath;

    return '/storage/' + profilePicturePath;
  }

  return '';
}

export function changeTerm(termId) {
  let urlSearchParams = new URLSearchParams(window.location.search);
  let routeData = { termId: termId }

  if (urlSearchParams.has('userId')) routeData.userId = urlSearchParams.get('userId')

  Inertia.get(route('dashboard', routeData));
}

export function changeAcademicYear(academicYearId) {
  Inertia.get(route('dashboard', { academicYearId: academicYearId }));
}

export const defaultProps = {
  user: Object,
  shouldShowDashboardHeading: Boolean,
  school: Object,
  term: Object,
  showTerm: Boolean,
  academicYearsWithTerms: Array,
  noticeBoardMessages: Object,
}
