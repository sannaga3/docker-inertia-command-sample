import { Head, Link, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

export default function CacheList({ auth, errors, cacheData = [] }) {
    const { post, processing } = useForm();

    const insertCache = () => {
        post(route("categories.insert_cache"));
    };

    const clearCache = () => {
        post(route("categories.clear_cache"));
    };

    return (
        <AuthenticatedLayout
            auth={auth}
            errors={errors}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    CategoryCacheList
                </h2>
            }
        >
            <Head title="CategoryCacheList" />
            <div className="flex flex-col items-start mx-20 mt-3">
                <div className="w-full flex items-center border-b border-gray-700">
                    <form className="space-x-5 px-8 pb-0.5">
                        <button
                            type="button"
                            onClick={insertCache}
                            disabled={processing}
                            className="font-medium text-sm text-gray-700"
                        >
                            insertCache
                        </button>
                        <button
                            type="button"
                            onClick={clearCache}
                            disabled={processing | (cacheData.length === 0)}
                            className="font-medium text-sm text-gray-700"
                        >
                            clearCache
                        </button>
                    </form>
                    <Link
                        href={route("categories.index")}
                        className="font-medium text-sm text-gray-700"
                    >
                        back
                    </Link>
                </div>

                <div className="flex justify-start flex-wrap gap-5 py-5 px-5">
                    {cacheData &&
                        cacheData.length > 0 &&
                        cacheData.map((data, index) => (
                            <div
                                key={index}
                                className="relative row-start-2 text-center border border-gray-500 rounded-md py-2 px-5"
                            >
                                <div>{data}</div>
                            </div>
                        ))}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
