import { useForm } from "@inertiajs/react";

export default function TaskItem({ task, index, setEditTaskIndex }) {
    const { delete: destroy } = useForm();

    return (
        <div className="grid grid-cols-12 space-x-2 px-3 mx-7 border-b border-gray-700 mt-3">
            <div>{index + 1}</div>
            <div className="col-span-2">{task.date}</div>
            <div className="col-span-1">{task.username}</div>
            <div className="col-span-1">{task.title}</div>
            <div className="col-span-2">{task.content}</div>
            <div className="col-span-2 flex space-x-2 overflow-x-scroll overflow-hidden px-1">
                {task?.categories?.length > 0 &&
                    task.categories.map((category, index) => (
                        <div
                            key={category.id}
                            className={`${
                                index % 2 === 0
                                    ? "text-indigo-500"
                                    : "text-indigo-800"
                            }`}
                        >
                            {category.name}
                        </div>
                    ))}
            </div>
            <div>{task.finished ? <>✔︎</> : <></>}</div>
            <div>
                <button
                    className="inline-flex items-center border border-transparent rounded-md font-semibold text-xs tracking-wides outline-none bg-yellow-500 text-white px-2 py-1 mb-2 hover:bg-yellow-700"
                    data-id={index}
                    onClick={(e) =>
                        setEditTaskIndex(e.currentTarget.dataset.id)
                    }
                >
                    edit
                </button>
            </div>
            <div>
                <button
                    className="inline-flex items-center border border-transparent rounded-md font-semibold text-xs tracking-wides outline-none bg-red-500 text-white px-2 py-1 mb-2 hover:bg-red-700"
                    onClick={() => destroy(route("tasks.destroy", task.id))}
                >
                    delete
                </button>
            </div>
        </div>
    );
}
