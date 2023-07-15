import NavLink from "@/Components/NavLink";

export default function Guest({ children }) {
    const pathname = location.pathname.slice(1);

    return (
        <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <NavLink href="/" className="absolute top-2 left-5 text-xl ">
                ← back
            </NavLink>

            <div className="text-4xl font-bold">{pathname}</div>

            <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {children}
            </div>
        </div>
    );
}
