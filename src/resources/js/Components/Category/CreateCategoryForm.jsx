import { Link, useForm } from "@inertiajs/react";
import InputLabel from "../InputLabel";
import TextInput from "../TextInput";
import InputError from "../InputError";
import { useState } from "react";
import PrimaryButton from "../PrimaryButton";

export default function CreateCategoryForm({
    errorMessages = null,
    prevRequestData = null,
}) {
    const { data, setData, post, processing, errors } = useForm(
        prevRequestData ?? { name_0: "" }
    );
    const [formLength, setFormLength] = useState(Object.keys(data).length);

    const addForm = () => {
        setFormLength(formLength + 1);
    };

    const reduceForm = () => {
        setFormLength(formLength - 1);
        const targetKey = Object.keys(data).pop();
        delete data[targetKey];

        setData(() => ({
            ...data,
        }));
    };

    const handleOnChange = (e) => {
        const { name, value } = e.target;

        setData((prevData) => ({
            ...prevData,
            [name]: value,
        }));
    };

    const submit = (e) => {
        e.preventDefault();

        post(route("categories.store"), {
            onSuccess: () => {
                setFormLength(1);
                setData(() => ({
                    name_0: "",
                }));
            },
        });
    };

    const keepCache = () => {
        post(route("categories.keep_cache"), {
            onSuccess: () => {
                setFormLength(1);
                setData(() => ({
                    name_0: "",
                }));
            },
        });
    };

    return (
        <form onSubmit={submit} className="flex flex-col px-10">
            <div className="flex justify-between border-b border-gray-700 mx-7 mt-3 px-20 pb-1">
                <InputLabel
                    htmlFor="name"
                    value="new Category"
                    className="col-start-2 col-span-2"
                />
                <div className="space-x-5">
                    <button
                        type="button"
                        onClick={keepCache}
                        className="font-medium text-sm text-gray-700"
                    >
                        keep
                    </button>
                    <Link
                        href={route("categories.cache_list")}
                        className="font-medium text-sm text-gray-700"
                    >
                        show
                    </Link>
                </div>
            </div>
            <div className="flex flex-col justify-center border-b border-gray-700 mx-7 mt-3 px-20">
                <div className="flex items-center space-x-3">
                    {[...Array(formLength)].map((_, index) => {
                        return (
                            <TextInput
                                key={index}
                                id={`name_${index}`}
                                type="string"
                                name={`name_${index}`}
                                value={data[`name_${index}`]}
                                className="h-6 text-xs mb-3 mt-0.5 p-2"
                                isFocused={true}
                                onChange={handleOnChange}
                            />
                        );
                    })}
                    {formLength < 5 && (
                        <button
                            type="button"
                            onClick={() => addForm()}
                            className="text-lg mb-0.5"
                        >
                            +
                        </button>
                    )}
                    {formLength > 1 && (
                        <button
                            type="button"
                            onClick={() => reduceForm()}
                            className="text-lg mb-0.5"
                        >
                            -
                        </button>
                    )}

                    <PrimaryButton disabled={processing}>submit</PrimaryButton>
                </div>
                <div className="pb-1">
                    {errorMessages ? (
                        errorMessages.map((error) => (
                            <div key={error}>
                                <InputError message={error} />
                            </div>
                        ))
                    ) : (
                        <InputError message={errors.name} />
                    )}
                </div>
            </div>
        </form>
    );
}
