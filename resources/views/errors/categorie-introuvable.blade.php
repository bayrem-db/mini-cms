<x-layout>
  <x-slot:title>Catégorie introuvable - Mini-CMS</x-slot:title>

  <x-page-header title="Catégorie introuvable" subtitle="Cette catégorie n'existe pas ou a été supprimée." />

  <x-alert type="info" title="Que faire ?" class="max-w-2xl">
    Choisissez une catégorie existante ci-dessous, ou parcourez les articles.
  </x-alert>

  @if ($categories->isNotEmpty())
  <section class="mt-8 max-w-2xl" aria-labelledby="categories-title">
    <h2 id="categories-title" class="text-xl font-bold mb-3">Catégories disponibles</h2>

    <ul class="space-y-2">
      @foreach ($categories as $category)
      <li>
        <a href="{{ route('posts.category', $category) }}" class="font-medium text-indigo-600 hover:underline">{{ $category->name }}</a>
      </li>
      @endforeach
    </ul>
  </section>
  @endif

  <p class="mt-6">
    <a href="{{ route('posts.index') }}" class="font-medium text-indigo-600 hover:underline">Voir tous les articles</a>
  </p>
</x-layout>
