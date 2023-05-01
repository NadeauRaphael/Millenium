// https://symfony.com/doc/current/form/form_collections.html
const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');
    item.className="list-unstyled";

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;
};
document
    .querySelectorAll('.add_category_link')
    .forEach(btn => {
        btn.addEventListener("click", addFormToCollection)
    });