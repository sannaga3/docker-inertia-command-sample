import { useForm } from "@inertiajs/react";
import InputLabel from "../InputLabel";
import TextInput from "../TextInput";
import InputError from "../InputError";
import Checkbox from "../Checkbox";
import PrimaryButton from "../PrimaryButton";

export default function CreateTaskForm() {
    const { data, setData, post, processing, errors } = useForm({
        title: "",
        content: "",
        date: new Date()
            .toLocaleDateString("ja-JP", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
            })
            .split("/")
            .join("-"),
        finished: false,
        published: true,
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

        post(route("tasks.store"));
    };

    return (
        <form onSubmit={submit} className="flex flex-col">
            <div className="grid grid-cols-12 space-x-2 px-3 mx-7 border-b border-gray-700 mt-3 pb-1">
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
                    className="col-span-2"
                />
                <InputLabel htmlFor="published" value="published" />
                <InputLabel htmlFor="finished" value="finished" />
            </div>

            <div className="grid grid-cols-12 space-x-2 px-3 mx-7 border-b border-gray-700 mt-3">
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

                <div className="col-span-2">
                    <TextInput
                        id="content"
                        type="text"
                        name="content"
                        value={data.content}
                        className="text-xs mb-3 mt-1"
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
