import { Inertia } from '@inertiajs/inertia';

export function getProfilePictureUrl(profilePicturePath) {
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

export const defaultProps = {
  user: Object,
  shouldShowDashboardHeading: Boolean,
  school: Object,
  term: Object,
  showTerm: Boolean,
  academicYearsWithTerms: Array,
  noticeBoardMessages: Object,
};
