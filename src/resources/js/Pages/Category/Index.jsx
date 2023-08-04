import { useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import CreateCategoryForm from "@/Components/Category/CreateCategoryForm";
import EditCategoryForm from "@/Components/Category/EditCategoryForm";
import InputError from "@/Components/InputError";

export default function Index(props) {
    const { delete: destroy } = useForm();
    const categories = props?.categories || [];
    const errors = props?.errors;
    const errorId = props?.errorId ? parseInt(props?.errorId) : null;
    const [editIndex, setEditIndex] = useState(errorId || null);
    const updatedId = props?.updatedId ? props?.updatedId : null;
    const prevRequestData = props?.prevRequestData
        ? props?.prevRequestData
        : null;

    useEffect(() => {
        if (errorId === null) setEditIndex(null);
        else updatedId && setEditIndex(null);
    }, [errorId, updatedId]);

    let errorMessages = null;
    if (errors.length > 0) errorMessages = errors.map((error) => error.name[0]);

    const handleCancelUpdateOrDestroy = (editIndex = null, categoryId) => {
        editIndex
            ? setEditIndex(null)
            : destroy(route("categories.destroy", categoryId));
    };

    return (
        <AuthenticatedLayout
            auth={props.auth}
            errors={props.errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    CategoryList
                </h2>
            }
        >
            <Head title="CategoryList" />

            <div className="py-12">
                <CreateCategoryForm
                    errorMessages={errorMessages}
                    prevRequestData={prevRequestData}
                />
                <div className="flex justify-start flex-wrap gap-5 py-5 mx-28">
                    {categories.length > 0 &&
                        categories.map((category, index) => (
                            <div
                                key={index}
                                className="relative row-start-2 text-center border border-gray-500 rounded-md py-2 px-5"
                            >
                                {editIndex && category.id === editIndex ? (
                                    <>
                                        <EditCategoryForm
                                            category={category}
                                            setEditIndex={setEditIndex}
                                        />
                                        {errorId === category.id && (
                                            <InputError message={errors.name} />
                                        )}
                                    </>
                                ) : (
                                    <div
                                        onClick={() => {
                                            setEditIndex(category.id);
                                        }}
                                        className={
                                            `hover:cursor-pointer z-10 ` +
                                                updatedId &&
                                            updatedId === category.id
                                                ? "text-orange-600"
                                                : ""
                                        }
                                    >
                                        {category.name}
                                    </div>
                                )}
                                <button
                                    className="absolute -top-1.5 right-1 text-red-500 z-10"
                                    onClick={() =>
                                        handleCancelUpdateOrDestroy(
                                            editIndex,
                                            category.id
                                        )
                                    }
                                >
                                    ×
                                </button>
                            </div>
                        ))}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
