import { useForm } from "@inertiajs/react";
import TextInput from "../TextInput";
import InputError from "../InputError";
import Checkbox from "../Checkbox";
import PrimaryButton from "../PrimaryButton";
import InputLabel from "../InputLabel";

export default function EditTaskModal({ task, setEditTaskIndex }) {
    const { data, setData, patch, processing, errors } = useForm({
        title: task.title,
        content: task.content,
        date: task.date,
        finished: task.finished,
        published: task.published,
    });

    const handleOnChange = (e) => {
        const { name, value, type, checked } = e.target;
        const newValue = type === "checkbox" ? checked : value;

        setData((prevData) => ({
            ...prevData,
            [name]: newValue,
        }));
    };

    const submit = (e) => {
        e.preventDefault();
        patch(route("tasks.update", task.id));
        setEditTaskIndex(null);
    };

    return (
        <form
            onSubmit={submit}
            className="flex flex-col bg-gray-100 border border-gray-800 rounded-md"
        >
            <div className="grid grid-cols-10 space-x-2 border-b border-gray-700 px-3 pb-1 mx-7 mt-3">
                <InputLabel
                    htmlFor="date"
                    value="date"
                    className="col-span-2"
                />
                <InputLabel
                    htmlFor="title"
                    value="title"
                    className="col-span-2"
                />
                <InputLabel
                    htmlFor="content"
                    value="content"
                    className="col-span-3"
                />
                <InputLabel htmlFor="published" value="published" />
                <InputLabel htmlFor="finished" value="finished" />
                <div className="col-span-1 flex justify-end">
                    <button
                        type="button"
                        onClick={() => setEditTaskIndex(null)}
                        className="col-end"
                    >
                        ✖️
                    </button>
                </div>
            </div>

            <div className="grid grid-cols-10 space-x-2 px-3 mx-7 mt-3">
                <div className="col-span-2">
                    <TextInput
                        id="date"
                        type="date"
                        name="date"
                        value={data.date}
                        className="text-xs mb-3 mt-1"
                        isFocused={true}
                        onChange={handleOnChange}
                    />
                    <InputError message={errors.date} className="mb-2" />
                </div>
                <div className="col-span-2">
                    <TextInput
                        id="title"
                        type="text"
                        name="title"
                        value={data.title}
                        className="text-xs mb-3 mt-1"
                        isFocused={true}
                        onChange={handleOnChange}
                    />
                    <InputError message={errors.title} className="mb-2" />
                </div>

                <div className="col-span-3">
                    <TextInput
                        id="content"
                        type="text"
                        name="content"
                        value={data.content}
                        className="w-5/6 h-8 text-xs px-2 mb-3 mt-1"
                        onChange={handleOnChange}
                    />
                    <InputError message={errors.content} className="mb-2" />
                </div>
                <div className="mt-2">
                    <Checkbox
                        name="published"
                        value={data.published}
                        onChange={handleOnChange}
                        checked={data.published}
                    />
                </div>
                <div className="mt-2">
                    <Checkbox
                        name="finished"
                        value={data.finished}
                        onChange={handleOnChange}
                        checked={data.finished}
                    />
                </div>
                <div className="mt-2">
                    <PrimaryButton disabled={processing}>submit</PrimaryButton>
                </div>
            </div>
        </form>
    );
}
