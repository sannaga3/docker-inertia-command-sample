import { useForm } from "@inertiajs/react";

export default function CreateCategoryForm({ category }) {
    const { data, setData, patch } = useForm({
        name: category.name,
    });

    const handleOnChange = (e) => {
        const { value } = e.target;

        setData(() => ({
            name: value,
        }));
    };

    const submit = (e) => {
        e.preventDefault();

        patch(route("categories.update", category.id));
    };

    return (
        <form onSubmit={submit} className="flex justify-center">
            <input
                id="name"
                type="name"
                name="name"
                value={data.name}
                className={
                    "w-5/6 h-5 text-xs border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 px-2"
                }
                onChange={handleOnChange}
            />
            <button className="absolute top-0 left-1 text-xs text-indigo-500">
                ○
            </button>
        </form>
    );
}
