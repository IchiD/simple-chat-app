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
  T extends { name: string; friend_id: string }
>(members: Ref<T[]>, perPage = 50) {
  const keyword = ref("");
  const sortKey = ref<SortKey>("name");
  const sortOrder = ref<SortOrder>("asc");
  const page = ref(1);

  const filtered = computed(() => {
    if (!keyword.value.trim()) return members.value;
    const kw = keyword.value.toLowerCase();
    return members.value.filter(
      (m) =>
        m.name.toLowerCase().includes(kw) ||
        m.friend_id.toLowerCase().includes(kw)
    );
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
