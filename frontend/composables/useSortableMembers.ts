import { computed, ref, type Ref } from "vue";

export type SortKey = "name" | "friend_id";
export type SortOrder = "asc" | "desc";

export interface PaginatedResult<T> {
  items: T[];
  page: number;
  totalPages: number;
  next: () => void;
  prev: () => void;
}

export function useSortableMembers<
  T extends {
    name: string;
    friend_id: string;
    owner_nickname?: string | null;
    pivot?: { owner_nickname?: string };
  }
>(members: Ref<T[]>, perPage = 50) {
  const keyword = ref("");
  const sortKey = ref<SortKey>("name");
  const sortOrder = ref<SortOrder>("asc");
  const page = ref(1);

  const filtered = computed(() => {
    if (!keyword.value.trim()) return members.value;
    const kw = keyword.value.toLowerCase();
    console.log("Debug: Filtering members with keyword:", kw);
    console.log("Debug: Members data:", members.value);

    const result = members.value.filter((m) => {
      const nameMatch = m.name.toLowerCase().includes(kw);
      const friendIdMatch = m.friend_id.toLowerCase().includes(kw);
      const nicknameMatch =
        (m.owner_nickname?.toLowerCase().includes(kw) ?? false) ||
        (m.pivot?.owner_nickname?.toLowerCase().includes(kw) ?? false);

      console.log(`Debug: Member ${m.name}:`, {
        name: m.name,
        friend_id: m.friend_id,
        owner_nickname: m.owner_nickname,
        pivot: m.pivot,
        nameMatch,
        friendIdMatch,
        nicknameMatch,
        overallMatch: nameMatch || friendIdMatch || nicknameMatch,
      });

      return nameMatch || friendIdMatch || nicknameMatch;
    });

    console.log("Debug: Filtered result:", result);
    return result;
  });

  const sorted = computed(() => {
    return [...filtered.value].sort((a, b) => {
      const valA = a[sortKey.value];
      const valB = b[sortKey.value];
      if (valA === valB) return 0;
      const compare = valA > valB ? 1 : -1;
      return sortOrder.value === "asc" ? compare : -compare;
    });
  });

  const totalPages = computed(() =>
    Math.max(1, Math.ceil(sorted.value.length / perPage))
  );

  const paginatedItems = computed(() => {
    const start = (page.value - 1) * perPage;
    return sorted.value.slice(start, start + perPage);
  });

  const next = () => {
    if (page.value < totalPages.value) page.value += 1;
  };
  const prev = () => {
    if (page.value > 1) page.value -= 1;
  };

  return {
    keyword,
    sortKey,
    sortOrder,
    page,
    totalPages,
    paginatedItems,
    next,
    prev,
  };
}
