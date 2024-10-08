<template>
  <div class="flex flex-col justify-start p-4">
    <div class="flex items-center gap-2">
      <ProfilePicture
        :profilePicturePath="selectedParam?.user.profile_picture_path"
        widthClass="w-24 sm:w-12"
        heightClass="h-24 sm:h-12"
      />
      <div>
        <span class="font-bold">
          {{ selectedParam?.user.name }}
        </span>
        {{ selectedParam?.text }}
      </div>
    </div>
    <div class="flex items-center justify-end gap-4">
      <button
        @click="declineRequest(selectedParam?.declineRequestRouteName)"
        class="flex items-center justify-center gap-2 rounded-lg border border-red-600 bg-red-50 px-2 py-1 text-sm text-red-600 shadow-sm hover:bg-red-100"
      >
        <XCircleIcon class="h-5 w-5" />
        Decline
      </button>
      <button
        @click="acceptRequest(selectedParam)"
        class="flex items-center justify-center gap-2 rounded-lg border border-green-600 bg-green-50 px-2 py-1 text-sm text-green-600 shadow-sm hover:bg-green-100"
      >
        <CheckCircleIcon class="h-5 w-5" />
        Accept
      </button>
    </div>
  </div>
</template>

<script setup>
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/outline';
import { Inertia } from '@inertiajs/inertia';

import { useNotifications } from '@/notifications.js';

const { isThereANewNotification } = useNotifications();

const props = defineProps({
  notification: Object,
});

const notificationParams = {
  'App\\Notifications\\AddAsParentRequest': {
    user: props.notification.data.child,
    text: ' wants to be added as one of your children',
    // acceptRequest: acceptAddAsParentRequest,
    acceptRequestRouteName: 'add_as_parent_request.accept_request',
    acceptRequestParams: {
      childId: props.notification.data.child?.id,
    },
    declineRequestRouteName: 'add_as_parent_request.decline_request',
  },
  'App\\Notifications\\AddAsChildRequest': {
    user: props.notification.data.parent,
    text: ' wants to be added as one of your parents',
    // acceptRequest: acceptAddAsChildRequest,
    acceptRequestRouteName: 'add_as_child_request.accept_request',
    acceptRequestParams: {
      parentId: props.notification.data.parent?.id,
    },
    declineRequestRouteName: 'add_as_child_request.decline_request',
  },
  'App\\Notifications\\JoinSchoolRequest': {
    user: props.notification.data.user,
    text:
      ' wants to join the school as a ' +
      props.notification.data.user?.default_user_type,
    acceptRequestParams: {
      userId: props.notification.data.user?.id,
    },
    acceptRequestRouteName: 'join_school_request.accept_request',
    declineRequestRouteName: 'join_school_request.decline_request',
  },
};

const selectedParam = notificationParams[props.notification.type];

const inertiaVisitData = {
  method: 'post',
  preserveState: false,
  onSuccess: (page) => (isThereANewNotification.value = false),
};

function acceptRequest(selectedParam) {
  Inertia.visit(
    route(selectedParam.acceptRequestRouteName, {
      ...selectedParam.acceptRequestParams,
      notificationId: props.notification.id,
    }),
    inertiaVisitData
  );
}

// function acceptAddAsParentRequest() {
//   Inertia.visit(
//     route('add_as_parent_request.accept_request', {
//       childId: props.notification.data.child.id,
//       notificationId: props.notification.id,
//     }),
//     inertiaVisitData
//   );
// }

// function acceptAddAsChildRequest() {
//   Inertia.visit(
//     route('add_as_child_request.accept_request', {
//       parentId: props.notification.data.parent.id,
//       notificationId: props.notification.id,
//     }),
//     inertiaVisitData
//   );
// }

function declineRequest(routeName) {
  Inertia.visit(
    route(routeName, {
      notificationId: props.notification.id,
    }),
    inertiaVisitData
  );
}
</script>
