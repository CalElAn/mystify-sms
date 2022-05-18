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
  Inertia.get(route('dashboard', { termId: termId }));
}

export function changeAcademicYear(academicYearId) {
  Inertia.get(route('dashboard', { academicYearId: academicYearId }));
}

export const defaultProps = {
  school: Object,
  term: Object,
  academicYearsWithTerms: Array,
  noticeBoardMessages: Object,
}
