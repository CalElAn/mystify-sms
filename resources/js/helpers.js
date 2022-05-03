import { Inertia } from '@inertiajs/inertia';

export function getProfilePictureUrl(user) {
  if (user) {
    if (user.profile_picture_path.includes('https://'))
      return user.profile_picture_path;

    return '/storage/' + user.profile_picture_path;
  }

  return '';
}

export function changeTerm(term_id) {
  Inertia.get(route('dashboard', { termId: term_id }));
}
