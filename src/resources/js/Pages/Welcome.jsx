import { Link, Head } from "@inertiajs/react";

export default function Welcome(props) {
    return (
        <>
            <Head title="Welcome" />
            <div className="flex justify-center items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
                {props.auth.user ? (
                    <Link
                        href={route("dashboard")}
                        className="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >
                        Dashboard
                    </Link>
                ) : (
                    <div className="flex flex-col justify-center items-center">
                        <div className="text-6xl text-gray-600 mb-20">
                            ☺︎ Welcome to this application ☺︎
                        </div>
                        <div className="space-x-20">
                            <Link
                                href={route("login")}
                                className="text-5xl font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white border-4 border-gray-600 hover:border-gray-900 rounded-lg px-7 py-3"
                            >
                                Log in
                            </Link>
                            <Link
                                href={route("register")}
                                className="text-5xl font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white border-4 border-gray-600 hover:border-gray-900 rounded-lg px-7 py-3"
                            >
                                Register
                            </Link>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
